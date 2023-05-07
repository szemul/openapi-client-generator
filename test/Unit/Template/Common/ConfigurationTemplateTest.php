<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Template\Common\ConfigurationTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ConfigurationTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString()
    {
        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            <?php

            declare(strict_types=1);

            namespace Root;

            class Configuration
            {
                public const API_KEY_HEADER_NAME = 'X-Api-Key';

                private string  $host;
                private string  $apiKeyHeaderName = self::API_KEY_HEADER_NAME;
                private ?string $apiKey           = null;

                public function __construct(string $host)
                {
                    $this->host = rtrim($host, '/');
                }

                public function getHost(): string
                {
                    return $this->host;
                }

                public function setHost(string $host): self
                {
                    $this->host = $host;
            
                    return $this;
                }

                public function getApiKeyHeaderName(): string
                {
                    return $this->apiKeyHeaderName;
                }

                public function setApiKeyHeaderName(string $apiKeyHeaderName): self
                {
                    $this->apiKeyHeaderName = $apiKeyHeaderName;
            
                    return $this;
                }

                public function getApiKey(): ?string
                {
                    return $this->apiKey;
                }

                public function setApiKey(?string $apiKey): self
                {
                    $this->apiKey = $apiKey;
            
                    return $this;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Configuration', $className);
    }

    private function getSut(): ConfigurationTemplate
    {
        return new ConfigurationTemplate($this->locationHelper);
    }
}
