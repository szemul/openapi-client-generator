<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Factory as TemplateFactory;

class Factory
{
    private FileHandler     $fileHandler;
    private Configuration   $configuration;
    private TemplateFactory $templateFactory;
    private TypeMapper      $typeMapper;

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        TemplateFactory $templateFactory,
        TypeMapper $typeMapper
    ) {
        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->typeMapper      = $typeMapper;
    }

    public function getApiGenerator(): ApiGenerator
    {
        return new ApiGenerator($this->fileHandler, $this->configuration, $this->templateFactory->getApiFactory());
    }

    public function getCommonGenerator(): CommonGenerator
    {
        return new CommonGenerator($this->fileHandler, $this->configuration, $this->templateFactory->getCommonFactory());
    }

    public function getExceptionGenerator(): ExceptionGenerator
    {
        return new ExceptionGenerator(
            $this->fileHandler,
            $this->configuration,
            $this->templateFactory->getExceptionFactory(),
            $this->typeMapper
        );
    }

    public function getModelGenerator(): ModelGenerator
    {
        return new ModelGenerator($this->fileHandler, $this->configuration, $this->templateFactory->getModelFactory(), $this->typeMapper);
    }
}
