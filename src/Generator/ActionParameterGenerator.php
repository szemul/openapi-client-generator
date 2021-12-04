<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Mapper\ParameterMapper;
use Emul\OpenApiClientGenerator\Template\Api\Factory;

class ActionParameterGenerator implements GeneratorInterface
{
    private FileHandler     $fileHandler;
    private Configuration   $configuration;
    private Factory         $templateFactory;
    private ParameterMapper $parameterMapper;
    private ClassHelper     $classHelper;

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        Factory $templateFactory,
        ParameterMapper $parameterMapper,
        ClassHelper $classHelper
    ) {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }

        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->parameterMapper = $parameterMapper;
        $this->classHelper     = $classHelper;
    }

    public function generate(): void
    {
        foreach ($this->configuration->getApiDoc()['paths'] as $methods) {
            foreach ($methods as $details) {
                $actionParameterClassName = $this->classHelper->getActionParameterClassName($details['tags'][0], $details['operationId']);
                $requestModelClassName    = null;

                /** @var Parameter[] $parameters */
                $parameters = [];

                if (!empty($details['requestBody']['content'])) {
                    $requestModel          = $details['requestBody']['content']['application/json']['schema']['$ref'];
                    $requestModelClassName = empty($requestModel) ? null : basename($requestModel);
                }

                if (!empty($details['parameters'])) {
                    foreach ($details['parameters'] as $parameterDetails) {
                        $parameters[] = $this->parameterMapper->mapParameter($parameterDetails);
                    }
                }

                $parameterTemplate = $this->templateFactory->getActionParameterTemplate(
                    $actionParameterClassName,
                    $requestModelClassName,
                    ...$parameters
                );

                $this->fileHandler->saveClassTemplateToFile($parameterTemplate);
                $this->configuration->getClassPaths()->addActionParameterClass($parameterTemplate->getClassName(true));
            }
        }
    }
}
