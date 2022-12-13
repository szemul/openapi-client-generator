<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\SchemaHelper;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\Factory;

class ApiGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory $templateFactory,
        private readonly ClassHelper $classHelper,
        private readonly SchemaHelper $schemaHelper
    ) {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }
    }

    public function generate(): void
    {
        /** @var ApiActionTemplate[][] $actionsByTag */
        $actionsByTag = [];

        foreach ($this->configuration->getApiDoc()['paths'] as $path => $methods) {
            foreach ($methods as $methodName => $details) {
                $operationId              = $details['operationId'];
                $actionParameterClassName = $this->classHelper->getActionParameterClassName($details['tags'][0], $operationId);

                $httpMethod = HttpMethod::createFromString(strtoupper($methodName));

                $responseIsList    = null;
                $responseClassName = $this->schemaHelper->getActionResponseClassName($details, $responseIsList);
                $actionTemplate    = $this->templateFactory->getApiActionTemplate(
                    $operationId,
                    $actionParameterClassName,
                    $path,
                    $httpMethod,
                    $responseIsList,
                    $responseClassName
                );

                foreach ($details['tags'] as $tag) {
                    $actionsByTag[$tag][] = $actionTemplate;
                }
            }
        }

        foreach ($actionsByTag as $tag => $actions) {
            $template = $this->templateFactory->getApiTemplate($tag, ...$actions);

            $this->fileHandler->saveClassTemplateToFile($template);
            $this->configuration->getClassPaths()->addApiClass($template->getClassName(true));
        }
    }
}
