<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Template\Common\Factory;

class CommonGenerator implements GeneratorInterface
{
    private FileHandler   $fileHandler;
    private Configuration $configuration;
    private Factory       $templateFactory;

    public function __construct(FileHandler $fileHandler, Configuration $configuration, Factory $templateFactory)
    {
        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
    }

    public function generate(): void
    {
        $this->generateArrayMapperFactory();
        $this->generateComposerJson();
        $this->generateConfiguration();
        $this->generateTJsonSerializable();
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

    private function generateTJsonSerializable(): void
    {
        $template = $this->templateFactory->getTJsonSerializableTemplate();
        $this->fileHandler->saveClassTemplateToFile($template);
    }
}
