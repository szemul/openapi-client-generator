<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\Factory;

class ApiGenerator implements GeneratorInterface
{
    private FileHandler   $fileHandler;
    private Configuration $configuration;
    private Factory       $templateFactory;
    private ClassHelper   $classHelper;

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        Factory $templateFactory,
        ClassHelper $classHelper
    ) {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }

        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->classHelper     = $classHelper;
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
                $responseClassName = $this->getResponseClassName($details, $responseIsList);

                $actionTemplate = $this->templateFactory->getApiActionTemplate(
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

    private function getResponseClassName(array $actionDetails, ?bool &$responseIsList): ?string
    {
        $responseClass = null;

        foreach ($actionDetails['responses'] as $statusCode => $response) {
            if ($statusCode >= 300) {
                continue;
            }

            if (!empty($response['content']['application/json']['schema'])) {
                $schema = $response['content']['application/json']['schema'];

                if (!empty($schema['$ref'])) {
                    $responseIsList = false;
                    $responseClass  = $this->classHelper->getModelClassname($schema['$ref']);
                } elseif ($schema['type'] === 'array') {
                    $responseIsList = true;
                    $responseClass  = $this->classHelper->getListModelClassname(basename($schema['items']['$ref']));
                }
            }
        }

        return $responseClass;
    }
}
