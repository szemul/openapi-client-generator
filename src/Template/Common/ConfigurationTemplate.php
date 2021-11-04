<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ConfigurationTemplate extends TemplateAbstract
{
    public function __toString(): string
    {
        return <<<CONFIGURATION
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getRootNamespace()};
            
            class Configuration
            {
                public const API_KEY_HEADER_NAME = 'X-API-KEY';
            
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
            
                public function setHost(string \$host): Configuration
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
}
