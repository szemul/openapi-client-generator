<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

class ApiTemplate extends TemplateAbstract
{
    private string $apiName;
    /** @var ApiActionTemplate[] */
    private array $actions;

    public function __construct(string $rootNamespace, string $apiTag, array $actions)
    {
        parent::__construct($rootNamespace);

        $this->apiName = ucfirst($apiTag);
        $this->actions = $actions;
    }

    public function __toString(): string
    {
        return <<<API
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getRootNamespace()};
            
            use GuzzleHttp\ClientInterface;
            use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
            use GuzzleHttp\Psr7\Request;
            use GuzzleHttp\Utils;
            {$this->getImports()}
            
            class {$this->apiName}Api
            {
                private Configuration   \$configuration;
                private ClientInterface \$httpClient;
                private array           \$defaultHeaders = [];
            
                public function __construct(Configuration \$configuration, ClientInterface \$httpClient, array \$defaultHeaders = [])
                {
                    \$this->configuration  = \$configuration;
                    \$this->httpClient     = \$httpClient;
                    \$this->defaultHeaders = \$defaultHeaders;
                }
            
            {$this->getActions()}
            }
            API;
    }

    private function getImports(): string
    {
        $importsArray = [];
        foreach ($this->actions as $action) {
            foreach ($action->getModelFullClassNames() as $fullClassName) {
                $importsArray[] = 'use ' . $fullClassName;
            }
        }

        return implode(PHP_EOL, $importsArray);
    }

    private function getActions(): string
    {
        $result = '';
        foreach ($this->actions as $action) {
            $result .= (string)$action . PHP_EOL;
        }

        return $result;
    }
}
