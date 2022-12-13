<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\Response;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Exception\Factory;
use Exception;

class ExceptionGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly FileHandler   $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory       $templateFactory,
        private readonly TypeMapper    $typeMapper
    ) {
    }

    public function generate(): void
    {
        $this->generatePropertyNotInitializedException();
        $this->generateRequestException();
        $this->generateRequestCodeExceptions();
    }

    private function generatePropertyNotInitializedException(): void
    {
        $template = $this->templateFactory->getPropertyNotInitializedExceptionTemplate();

        $this->fileHandler->saveClassTemplateToFile($template);
    }

    private function generateRequestException(): void
    {
        $template = $this->templateFactory->getRequestExceptionTemplate();

        $this->fileHandler->saveClassTemplateToFile($template);
    }

    private function generateRequestCodeExceptions(): void
    {
        foreach ($this->gatherErrorResponsesByCode() as $errorResponse) {
            if (empty($this->configuration->getApiDoc()['components']['schemas'][$errorResponse->getSchemaName()])) {
                $template = $this->templateFactory->getRequestCodeExceptionTemplate($errorResponse->getStatusCode());
            } else {
                $errorSchema = $this->configuration->getApiDoc()['components']['schemas'][$errorResponse->getSchemaName()];
                $properties  = [];

                foreach ($errorSchema['properties'] as $propertyName => $propertyDetails) {
                    $type         = $this->typeMapper->mapApiDocDetailsToPropertyType($propertyName, $propertyDetails);
                    $description  = $propertyDetails['description'] ?? null;
                    $properties[] = $this->templateFactory->getRequestExceptionPropertyTemplate($propertyName, $type, $description);
                }

                $template = $this->templateFactory->getRequestCodeExceptionTemplate($errorResponse->getStatusCode(), ...$properties);
            }


            $this->fileHandler->saveClassTemplateToFile($template);
        }
    }

    private function gatherErrorResponsesByCode(): array
    {
        /** @var Response[] $errorResponses */
        $errorResponses = [];

        foreach ($this->configuration->getApiDoc()['paths'] as $methods) {
            foreach ($methods as $method) {
                if (empty($method['responses'])) {
                    continue;
                }

                foreach ($method['responses'] as $responseCode => $response) {
                    if ($responseCode < 400) {
                        continue;
                    }

                    $schemaName       = empty($response['content']['application/json']['schema']['$ref'])
                        ? null
                        : basename($response['content']['application/json']['schema']['$ref']);
                    $description      = $response['description'] ?? null;
                    $errorResponses[] = new Response((int)$responseCode, $description, $schemaName);
                }
            }
        }

        return $errorResponses;
    }
}
