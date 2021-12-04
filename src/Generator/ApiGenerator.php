<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterIn;
use Emul\OpenApiClientGenerator\Exception\GeneratorNotNeededException;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\Factory;

class ApiGenerator implements GeneratorInterface
{
    private FileHandler   $fileHandler;
    private Configuration $configuration;
    private Factory       $templateFactory;
    private TypeMapper    $typeMapper;

    public function __construct(
        FileHandler $fileHandler,
        Configuration $configuration,
        Factory $templateFactory,
        TypeMapper $typeMapper
    ) {
        if (empty($configuration->getApiDoc()['paths'])) {
            throw new GeneratorNotNeededException();
        }

        $this->fileHandler     = $fileHandler;
        $this->configuration   = $configuration;
        $this->templateFactory = $templateFactory;
        $this->typeMapper      = $typeMapper;
    }

    public function generate(): void
    {
        /** @var ApiActionTemplate[][] $actionsByTag */
        $actionsByTag = [];

        foreach ($this->configuration->getApiDoc()['paths'] as $path => $methods) {
            foreach ($methods as $methodName => $details) {
                $operationId           = $details['operationId'];
                $requestModel          = $details['requestBody']['content']['application/json']['schema']['$ref'] ?? null;
                $parameters            = $this->getParameters($operationId, ...($details['parameters'] ?? []));
                $requestModelClassName = empty($requestModel) ? null : basename($requestModel);
                $httpMethod            = HttpMethod::createFromString(strtoupper($methodName));

                $actionTemplate = $this->templateFactory->getApiActionTemplate(
                    $operationId,
                    $requestModelClassName,
                    $path,
                    $httpMethod,
                    ...$parameters
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

    /**
     * @return Parameter[]
     */
    private function getParameters(string $operationId, array ...$parameters): array
    {
        return array_map(
            fn (array $parameter) => new Parameter(
                $parameter['name'],
                ParameterIn::createFromString($parameter['in']),
                $parameter['required'] ?? true,
                $this->typeMapper->mapApiDocDetailsToPropertyType(
                    $parameter['name'],
                    $parameter['schema'],
                    $operationId
                )
            ),
            $parameters
        );
    }
}
