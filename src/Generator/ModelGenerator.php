<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\SchemaHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\Factory;
use Exception;

class ModelGenerator implements GeneratorInterface
{

    public function __construct(
        private readonly FileHandler   $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory       $templateFactory,
        private readonly TypeMapper    $typeMapper,
        private readonly ClassHelper   $classHelper,
        private readonly SchemaHelper  $schemaHelper
    ) {
        if (empty($configuration->getApiDoc()['components']['schemas'])) {
            throw new GeneratorNotNeededException();
        }
    }

    public function generate(): void
    {
        $this->fileHandler->saveClassTemplateToFile($this->templateFactory->getModelAbstractTemplate());
        $this->fileHandler->saveClassTemplateToFile($this->templateFactory->getResponseListInterfaceTemplate());
        $this->generateResponseLists();

        $schemas = $this->configuration->getApiDoc()['components']['schemas'];

        foreach ($schemas as $schemaName => $schema) {
            if (!empty($schema['allOf'])) {
                $schema         = $this->schemaHelper->uniteAllOfSchema($schemas, $schemaName);
                $schema['type'] = 'object';
            }

            $schemaType = $schema['type'] ?? null;

            if (empty($schemaType)) {
                throw new Exception('No type defined for schema: ' . $schemaName);
            }

            if ($schemaType === 'string' && !empty($schema['enum'])) {
                $this->generateEnum($schemaName, $schema);
            } else {
                $this->generateModel($schemaName, $schema);
            }
        }
    }

    private function generateModel(string $schemaName, array $schema)
    {
        $propertyTemplates = [];
        $schemaType        = $schema['type'];

        if ($schemaType === 'array') {
            $className = $this->classHelper->getModelClassname(basename($schema['items']['$ref']));
            $template  = $this->templateFactory->getResponseListTemplate($className);
        } elseif ($schemaType === 'object') {
            foreach ($schema['properties'] as $propertyName => $details) {
                $fullPropertyName = $schemaName . '_' . $propertyName;
                $type             = $this->typeMapper->mapApiDocDetailsToPropertyType($fullPropertyName, $details);
                $description      = $details['description'] ?? null;

                $propertyTemplates[] = $this->templateFactory->getModelPropertyTemplate(
                    $propertyName,
                    $type,
                    $this->isRequired($schema, $propertyName),
                    $description,
                );

                if (!empty($details['enum'])) {
                    $this->generateEnum($fullPropertyName, $details);
                }
            }

            $template = $this->templateFactory->getModelTemplate($schemaName, ...$propertyTemplates);
        } else {
            throw new Exception('Unhandled type ' . $schemaType);
        }

        $filePath = $template->getDirectory() . $template->getClassName() . '.php';

        $this->fileHandler->saveFile($filePath, (string)$template);
        $this->configuration->getClassPaths()->addModelClass($template->getClassName(true));
    }

    private function generateEnum(string $propertyName, array $details): void
    {
        $template = $this->templateFactory->getEnumTemplate($propertyName, ...$details['enum']);
        $filePath = $template->getDirectory() . $template->getClassName() . '.php';

        $this->fileHandler->saveFile($filePath, (string)$template);
        $this->configuration->getClassPaths()->addEntityClass($template->getClassName(true));
    }

    private function isRequired(array $schema, string $propertyName): bool
    {
        if (empty($schema['required'])) {
            return false;
        }

        return in_array($propertyName, $schema['required']);
    }

    private function generateResponseLists()
    {
        foreach ($this->configuration->getApiDoc()['paths'] as $methods) {
            foreach ($methods as $details) {
                foreach ($details['responses'] as $statusCode => $response) {
                    if ($statusCode >= 300 || empty($response['content']['application/json']['schema']['type'])) {
                        continue;
                    }

                    $schema = $response['content']['application/json']['schema'];
                    if ($schema['type'] === 'array') {
                        $className = $this->classHelper->getModelClassname(basename($schema['items']['$ref']));
                        $template  = $this->templateFactory->getResponseListTemplate($className);

                        $this->fileHandler->saveClassTemplateToFile($template);
                    }
                }
            }
        }
    }
}
