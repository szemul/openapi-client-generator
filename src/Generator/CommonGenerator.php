<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Template\Common\Factory;

class CommonGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory $templateFactory
    ) {
    }

    public function generate(): void
    {
        $this->generateArrayMapperFactory();
        $this->generateComposerJson();
        $this->generateConfiguration();
        $this->generateJsonSerializableTrait();
    }

    private function generateArrayMapperFactory(): void
    {
        $template = $this->templateFactory->getArrayMapperFactoryTemplate(...$this->configuration->getClassPaths()->getEntityClasses());
        $this->fileHandler->saveClassTemplateToFile($template);
    }

    private function generateComposerJson(): void
    {
        $description = 'Client for ' . $this->configuration->getApiDoc()['info']['title'];

        $template = $this->templateFactory->getComposerJsonTemplate(
            $this->configuration->getComposer()->getVendorName(),
            $this->configuration->getComposer()->getProjectName(),
            $description
        );

        $filePath = $this->configuration->getPaths()->getTargetRootPath() . 'composer.json';

        $this->fileHandler->saveFile($filePath, (string)$template);
    }

    private function generateConfiguration(): void
    {
        $template = $this->templateFactory->getConfigurationTemplate();
        $this->fileHandler->saveClassTemplateToFile($template);
    }

    private function generateJsonSerializableTrait(): void
    {
        $template = $this->templateFactory->getJsonSerializableTraitTemplate();
        $this->fileHandler->saveClassTemplateToFile($template);
    }
}
