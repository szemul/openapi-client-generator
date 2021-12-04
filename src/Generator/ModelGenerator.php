<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\Factory;

class ModelGenerator implements GeneratorInterface
{
    private FileHandler   $fileHandler;
    private Factory       $templateFactory;
    private Configuration $configuration;
    private TypeMapper    $typeMapper;
    private array         $parameterEnums = [];

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        Factory $templateFactory,
        TypeMapper $typeMapper
    ) {
        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->typeMapper      = $typeMapper;

        $this->populateParameterEnums();

        if (!$this->isGeneratorNeeded()) {
            throw new GeneratorNotNeededException();
        }
    }

    private function populateParameterEnums(): void
    {
        foreach ($this->configuration->getApiDoc()['paths'] as $path) {
            foreach ($path as $method) {
                if (empty($method['parameters'])) {
                    continue;
                }

                foreach ($method['parameters'] as $parameter) {
                    if (!empty($parameter['schema']['enum'])) {
                        $this->parameterEnums[] = [
                            'name'        => $parameter['name'],
                            'operationId' => $method['operationId'],
                            'schema'      => $parameter['schema'],
                        ];
                    }
                }
            }
        }
    }

    private function isGeneratorNeeded(): bool
    {
        return !empty($this->parameterEnums) || !empty($this->configuration->getApiDoc()['components']['schemas']);
    }

    public function generate(): void
    {
        $this->generateModelAbstract();

        foreach ($this->configuration->getApiDoc()['components']['schemas'] as $schemaName => $schema) {
            $this->generateModel($schemaName, $schema);
        }

        foreach ($this->parameterEnums as $enum) {
            $this->generateEnum($enum['name'], $enum['operationId'], $enum['schema']);
        }
    }

    private function generateModelAbstract(): void
    {
        $model    = $this->templateFactory->getModelAbstractTemplate();
        $filePath = $model->getDirectory() . $model->getClassName() . '.php';

        $this->fileHandler->saveFile($filePath, (string)$model);
    }

    private function generateModel(string $schemaName, array $schema)
    {
        $propertyTemplates = [];

        foreach ($schema['properties'] as $propertyName => $details) {
            $type        = $this->typeMapper->mapApiDocDetailsToPropertyType($propertyName, $details);
            $description = $details['description'] ?? null;

            $propertyTemplates[] = $this->templateFactory->getModelPropertyTemplate(
                $propertyName,
                $type,
                $this->isRequired($schema, $propertyName),
                $description,
            );

            if (!empty($details['enum'])) {
                $this->generateEnum($propertyName, '', $details);
            }
        }

        $template = $this->templateFactory->getModelTemplate($schemaName, ...$propertyTemplates);
        $filePath = $template->getDirectory() . $template->getClassName() . '.php';

        $this->fileHandler->saveFile($filePath, (string)$template);
        $this->configuration->getClassPaths()->addModelClass($template->getClassName(true));
    }

    private function generateEnum(string $propertyName, string $namespace, array $details): void
    {
        $template = $this->templateFactory->getEnumTemplate($propertyName, $namespace, ...$details['enum']);
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
}
