<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ApiTemplate extends ClassTemplateAbstract
{
    private string $apiName;
    /** @var ApiActionTemplate[] */
    private array $actions = [];

    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper,
        string $apiTag,
        ApiActionTemplate ...$actions
    ) {
        $this->apiName = $this->stringHelper->convertToClassName($apiTag);
        $this->actions = $actions;
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getApiPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getApiNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->apiName . 'Api';
    }

    public function __toString(): string
    {
        return <<<API
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Psr\Http\Client\ClientInterface;
            use Psr\Http\Message\RequestFactoryInterface;
            use Psr\Http\Message\StreamFactoryInterface;
            use {$this->locationHelper->getRootNamespace()}\Configuration;
            use {$this->locationHelper->getExceptionNamespace()}\RequestException;
            {$this->getImports()}
            
            class {$this->getClassName()}
            {
                private Configuration           \$configuration;
                private ClientInterface         \$httpClient;
                private RequestFactoryInterface \$requestFactory;
                private StreamFactoryInterface  \$streamFactory;
                private array                   \$defaultHeaders = [];

                public function __construct(
                    Configuration \$configuration,
                    ClientInterface \$httpClient,
                    RequestFactoryInterface \$requestFactory,
                    StreamFactoryInterface \$streamFactory,
                    array \$defaultHeaders = []
                ) {
                    \$this->configuration  = \$configuration;
                    \$this->httpClient     = \$httpClient;
                    \$this->requestFactory = \$requestFactory;
                    \$this->streamFactory  = \$streamFactory;
                    \$this->defaultHeaders = \$defaultHeaders;
                }
            
            {$this->getActions()}
            {$this->getResponseHandlerMethods()}
            }
            API;
    }

    private function getImports(): string
    {
        $importsArray = [];
        foreach ($this->actions as $action) {
            $importsArray[] = 'use ' . $action->getParameterFullClassName() . ';';

            foreach ($action->getClassesToImport() as $class) {
                $importsArray[] = 'use ' . $class . ';';
            }
        }

        sort($importsArray);

        return implode(PHP_EOL, array_unique($importsArray));
    }

    private function getActions(): string
    {
        $result = '';
        foreach ($this->actions as $action) {
            $result .= $action . PHP_EOL;
        }

        return $result;
    }

    private function getResponseHandlerMethods(): string
    {
        $result = '';
        foreach ($this->actions as $action) {
            $result .= implode(PHP_EOL, $action->getResponseHandlerMethods());
        }

        return $result;
    }
}
