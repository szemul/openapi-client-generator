<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Mapper;

use Carbon\CarbonInterface;
use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use InvalidArgumentException;

class TypeMapper
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper,
        private readonly ClassHelper $classHelper
    ) {
    }

    public function mapApiDocDetailsToPropertyType(string $name, array $details): PropertyType
    {
        // Handling the unnecessary usage of oneOf at nullable objects
        if (!empty($details['oneOf'])) {
            $details['$ref'] = $details['oneOf'][0]['$ref'];
        }
        $typeString  = $details['type'] ?? null;
        $scalarTypes = ['string', 'integer', 'number', 'boolean'];

        if (!empty($details['$ref'])) {
            $subModelName   = $this->classHelper->getModelClassname($details['$ref']);
            $subModelSchema = $this->configuration->getApiDoc()['components']['schemas'][$subModelName] ?? [];
            $schemaType     = $subModelSchema['type'] ?? '';

            if ($schemaType === 'string' && !empty($subModelSchema['enum'])) {
                $type = PropertyType::object($this->locationHelper->getEnumNamespace() . '\\' . $subModelName);
            } else {
                $type = PropertyType::object($this->locationHelper->getModelNamespace() . '\\' . $subModelName);
            }
        } elseif ($typeString === 'array') {
            if (!empty($details['items'])) {
                $arrayItemType = $this->mapApiDocDetailsToPropertyType($name, $details['items']);
            } else {
                $arrayItemType = PropertyType::string();
            }

            $type = PropertyType::array($arrayItemType);
        } elseif ($typeString === 'object') {
            $type = PropertyType::array(null);
        } elseif (in_array($typeString, $scalarTypes)) {
            if (!empty($details['enum'])) {
                $enumName = $this->stringHelper->convertToClassName($name);
                $type     = PropertyType::object($this->locationHelper->getEnumNamespace() . '\\' . $enumName);
            } elseif (!empty($details['format']) && $details['format'] === 'date-time') {
                $type = PropertyType::object(CarbonInterface::class);
            } else {
                switch ($typeString) {
                    case 'string':
                        $type = PropertyType::string();
                        break;

                    case 'integer':
                        $type = PropertyType::int();
                        break;

                    case 'number':
                        $type = PropertyType::float();
                        break;

                    case 'boolean':
                        $type = PropertyType::bool();
                        break;
                }
            }
        } else {
            throw new InvalidArgumentException('Unknown type: ' . $typeString);
        }

        return $type;
    }

    public function mapModelPropertyTemplateToPhp(ModelPropertyTemplate $template): string
    {
        if (
            $template->getType()->isScalar()
            || (string)$template->getType() === PropertyType::ARRAY
        ) {
            $phpType = ($template->isRequired() ? '' : '?') . $template->getType();
        } elseif ((string)$template->getType() === PropertyType::OBJECT) {
            $phpType = ($template->isRequired() ? '' : '?') . $template->getType()->getObjectClassname(false);
        } else {
            throw new InvalidArgumentException('Unhandled property type: ' . $template->getType());
        }

        return $phpType;
    }

    public function mapModelPropertyTemplateToDoc(ModelPropertyTemplate $template): string
    {
        if ($template->getType()->isScalar()) {
            $docType = $template->getType() . ($template->isRequired() ? '' : '|null');
        } elseif ((string)$template->getType() === PropertyType::OBJECT) {
            $docType = '\\' . $template->getType()->getObjectClassname() . ($template->isRequired() ? '' : '|null');
        } elseif ((string)$template->getType() === PropertyType::ARRAY) {
            $arrayItemType = $this->getArrayItemType($template->getType());
            $docType       = empty($arrayItemType) ? 'array' : $arrayItemType . '[]';
        } else {
            throw new InvalidArgumentException('Unhandled property type: ' . $template->getType());
        }

        return $docType;
    }

    public function mapParameterToPropertyType(array $parameterDetails): PropertyType
    {
        if (empty($parameterDetails['schema'])) {
            throw new InvalidArgumentException('Unable to retrieve type of ' . $parameterDetails['name']);
        }
        $typeString = $parameterDetails['schema']['type'];

        switch ($typeString) {
            case 'string':
                $type = PropertyType::string();
                break;

            case 'integer':
                $type = PropertyType::int();
                break;

            case 'number':
                $type = PropertyType::float();
                break;

            case 'boolean':
                $type = PropertyType::bool();
                break;

            default:
                throw new InvalidArgumentException("Unknown type $typeString");
        }

        return $type;
    }

    public function getArrayItemType(PropertyType $type): ?string
    {
        if (
            is_null($type->getArrayItemType())
            || (string)$type->getArrayItemType() === PropertyType::ARRAY
        ) {
            return null;
        } elseif ($type->getArrayItemType()->isScalar()) {
            return (string)$type->getArrayItemType();
        } else {
            return '\\' . $type->getArrayItemType()->getObjectClassname();
        }
    }
}
