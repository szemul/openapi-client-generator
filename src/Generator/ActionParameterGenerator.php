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
use Exception;

class ActionParameterGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly FileHandler     $fileHandler,
        private readonly Configuration   $configuration,
        private readonly Factory         $templateFactory,
        private readonly ParameterMapper $parameterMapper,
        private readonly ClassHelper     $classHelper
    ) {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }
    }

    public function generate(): void
    {
        foreach ($this->configuration->getApiDoc()['paths'] as $methods) {
            foreach ($methods as $method) {
                $this->validateMethodDefinition($method);

                $actionParameterClassName = $this->classHelper->getActionParameterClassName($method['tags'][0], $method['operationId']);
                $requestModelClassName    = null;

                /** @var Parameter[] $parameters */
                $parameters = [];

                if (!empty($method['requestBody']['content'])) {
                    $requestModel          = $method['requestBody']['content']['application/json']['schema']['$ref'];
                    $requestModelClassName = empty($requestModel) ? null : basename($requestModel);
                }

                if (!empty($method['parameters'])) {
                    foreach ($method['parameters'] as $parameterDetails) {
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

    private function validateMethodDefinition(array $method): void
    {
        if (empty($method['tags'][0])) {
            throw new Exception('Tags are mandatory');
        } elseif (empty($method['operationId'])) {
            throw new Exception('operationId is mandatory');
        }
    }
}
