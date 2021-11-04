<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\PropertyType\Type;
use Emul\OpenApiClientGenerator\Template\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Template\ModelTemplate;
use Exception;
use InvalidArgumentException;

class ModelGenerator
{
    private string $moduleDirectoryPath;
    private string $rootNamespace;
    private array  $schemas = [];

    public function __construct(array $apiDoc, string $targetRootPath, string $rootNamespace)
    {
        if (empty($apiDoc['components']['schemas'])) {
            throw new GeneratorNotNeededException();
        }

        $this->schemas             = $apiDoc['components']['schemas'];
        $this->moduleDirectoryPath = rtrim($targetRootPath, '/') . '/Model/';
        $this->rootNamespace       = $rootNamespace;

        $this->initializeDirectory();
    }

    public function generate(): void
    {
        foreach ($this->schemas as $schemaName => $schema) {
            $this->generateModel($schemaName, $schema);
        }
    }

    private function generateModel(string $schemaName, array $schema)
    {
        $modelPropertyTemplates = [];

        foreach ($schema['properties'] as $propertyName => $details) {
            $type        = $this->getType($details);
            $description = $details['description'] ?? null;

            $modelPropertyTemplates[] = new ModelPropertyTemplate(
                $this->rootNamespace,
                $propertyName,
                $type,
                $this->isRequired($schema, $propertyName),
                $description
            );
        }

        $modelTemplate = new ModelTemplate($this->rootNamespace, $schemaName, ...$modelPropertyTemplates);

        file_put_contents($this->moduleDirectoryPath . $schemaName . '.php', (string)$modelTemplate);
    }

    private function getType(array $details): Type
    {
        // Handling the unnecessary usage of onOf at nullable objects
        if (!empty($details['oneOf'])) {
            $details['$ref'] = $details['oneOf'][0]['$ref'];
        }
        $typeString  = $details['type'] ?? null;
        $scalarTypes = ['string', 'integer', 'number', 'boolean'];

        if (!empty($details['$ref'])) {
            $subModelName = basename($details['$ref']);
            $type         = Type::object($this->rootNamespace . '\\Model\\' . $subModelName);
        } elseif ($typeString === 'array') {
            if (!empty($details['items'])) {
                $arrayItemType = $this->getType($details['items']);
            } else {
                $arrayItemType = Type::string();
            }

            $type = Type::array($arrayItemType);
        } elseif ($typeString === 'object') {
            $type = Type::array(Type::string());
        } elseif (in_array($typeString, $scalarTypes)) {
            switch ($typeString) {
                case 'string':
                    $type = Type::string();
                    break;

                case 'integer':
                    $type = Type::int();
                    break;

                case 'number':
                    $type = Type::float();
                    break;

                case 'boolean':
                    $type = Type::bool();
                    break;
            }
        } else {
            var_dump($details);
            exit;
            throw new InvalidArgumentException('Unknown type: ' . $typeString);
        }

        return $type;
    }

    private function isRequired(array $schema, string $propertyName): bool
    {
        if (empty($schema['required'])) {
            return false;
        }

        return in_array($propertyName, $schema['required']);
    }

    private function initializeDirectory(): void
    {
        if (file_exists($this->moduleDirectoryPath)) {
            return;
        }

        $directoryCreated = mkdir($this->moduleDirectoryPath);

        if (!$directoryCreated) {
            throw new Exception('Failed to create ' . $this->moduleDirectoryPath);
        }
    }
}
