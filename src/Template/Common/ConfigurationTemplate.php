<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ConfigurationTemplate extends ClassTemplateAbstract
{
    public function __toString(): string
    {
        return <<<CONFIGURATION
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            class {$this->getClassName()}
            {
                public const API_KEY_HEADER_NAME = 'X-Api-Key';
            
                private string  \$host;
                private string  \$apiKeyHeaderName = self::API_KEY_HEADER_NAME;
                private ?string \$apiKey           = null;
            
                public function __construct(string \$host)
                {
                    \$this->host = rtrim(\$host, '/');
                }
            
                public function getHost(): string
                {
                    return \$this->host;
                }
            
                public function setHost(string \$host): self
                {
                    \$this->host = \$host;
            
                    return \$this;
                }
            
                public function getApiKeyHeaderName(): string
                {
                    return \$this->apiKeyHeaderName;
                }
            
                public function setApiKeyHeaderName(string \$apiKeyHeaderName): self
                {
                    \$this->apiKeyHeaderName = \$apiKeyHeaderName;
            
                    return \$this;
                }
            
                public function getApiKey(): ?string
                {
                    return \$this->apiKey;
                }
            
                public function setApiKey(?string \$apiKey): self
                {
                    \$this->apiKey = \$apiKey;
            
                    return \$this;
                }
            }
            CONFIGURATION;
    }

    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getRootPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getRootNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'Configuration';
    }
}
