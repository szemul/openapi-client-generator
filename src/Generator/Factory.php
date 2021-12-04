<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Mapper\ParameterMapper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Factory as TemplateFactory;

class Factory
{
    private FileHandler     $fileHandler;
    private Configuration   $configuration;
    private TemplateFactory $templateFactory;
    private TypeMapper      $typeMapper;
    private ParameterMapper $parameterMapper;
    private ClassHelper     $classHelper;

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        TemplateFactory $templateFactory,
        TypeMapper $typeMapper,
        ParameterMapper $parameterMapper,
        ClassHelper $classHelper
    ) {
        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->typeMapper      = $typeMapper;
        $this->parameterMapper = $parameterMapper;
        $this->classHelper     = $classHelper;
    }

    public function getApiGenerator(): ApiGenerator
    {
        return new ApiGenerator(
            $this->fileHandler,
            $this->configuration,
            $this->templateFactory->getApiFactory(),
            $this->classHelper
        );
    }

    public function getActionParameterGenerator(): ActionParameterGenerator
    {
        return new ActionParameterGenerator(
            $this->fileHandler,
            $this->configuration,
            $this->templateFactory->getApiFactory(),
            $this->parameterMapper,
            $this->classHelper
        );
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
        return new ModelGenerator(
            $this->fileHandler,
            $this->configuration,
            $this->templateFactory->getModelFactory(),
            $this->typeMapper,
            $this->classHelper
        );
    }
}
