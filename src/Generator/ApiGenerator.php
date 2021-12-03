<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\Factory;

class ApiGenerator implements GeneratorInterface
{
    private FileHandler   $fileHandler;
    private Configuration $configuration;
    private Factory       $templateFactory;

    public function __construct(FileHandler $fileHandler, Configuration $configuration, Factory $templateFactory)
    {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }

        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
    }

    public function generate(): void
    {
        /** @var ApiActionTemplate[][] $actionsByTag */
        $actionsByTag = [];

        foreach ($this->configuration->getApiDoc()['paths'] as $path => $methods) {
            foreach ($methods as $methodName => $details) {
                $operationId           = $details['operationId'];
                $requestModel          = $details['requestBody']['content']['application/json']['schema']['$ref'];
                $requestModelClassName = empty($requestModel) ? null : basename($requestModel);
                $httpMethod            = HttpMethod::createFromString(strtoupper($methodName));

                $actionTemplate = $this->templateFactory->getApiActionTemplate(
                    $operationId,
                    $requestModelClassName,
                    $path,
                    $httpMethod
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
