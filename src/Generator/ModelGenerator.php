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

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        Factory $templateFactory,
        TypeMapper $typeMapper
    ) {
        if (empty($configuration->getApiDoc()['components']['schemas'])) {
            throw new GeneratorNotNeededException();
        }

        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->typeMapper      = $typeMapper;
    }

    public function generate(): void
    {
        $this->generateModelAbstract();

        foreach ($this->configuration->getApiDoc()['components']['schemas'] as $schemaName => $schema) {
            $this->generateModel($schemaName, $schema);
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
                $this->generateEnum($propertyName, $details);
            }
        }

        $template = $this->templateFactory->getModelTemplate($schemaName, ...$propertyTemplates);
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
}
