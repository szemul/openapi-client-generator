<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\SchemaHelper;
use Emul\OpenApiClientGenerator\Mapper\ParameterMapper;
use Emul\OpenApiClientGenerator\Template\Api\Factory;
use Exception;

class ActionParameterGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory $templateFactory,
        private readonly ParameterMapper $parameterMapper,
        private readonly ClassHelper $classHelper,
        private readonly SchemaHelper $schemaHelper,
        private readonly ModelGenerator $modelGenerator
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
                    $schema = $method['requestBody']['content']['application/json']['schema'];

                    if (!empty($schema['$ref'])) {
                        $requestModel          = $schema['$ref'];
                        $requestModelClassName = basename($requestModel);
                    } else {
                        $requestModel = $method['operationId'] . 'Request';
                        $this->modelGenerator->generateModel($requestModel, $schema, false);
                    }
                }

                if (!empty($method['parameters'])) {
                    foreach ($method['parameters'] as $parameterDetails) {
                        if (array_key_exists('$ref', $parameterDetails)) {
                            $parameterDetails = $this->schemaHelper->getReferencedValue($parameterDetails['$ref'], $this->configuration->getApiDoc());
                        }

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
        if (empty($method['operationId'])) {
            throw new Exception('operationId is mandatory');
        } elseif (empty($method['tags'][0])) {
            throw new Exception('Tags are mandatory for ' . $method['operationId']);
        }
    }
}
