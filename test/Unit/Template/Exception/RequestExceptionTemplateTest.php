<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Exception;

use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class RequestExceptionTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldRenderProperly()
    {
        $sut = $this->getSut();

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root\Exception;
            
            use Carbon\CarbonInterface;
            use Exception;
            
            class RequestException extends Exception
            {
                private string $responseBody;
                private array  $responseHeaders = [];
                private string $requestUrl;
                private string $requestMethod;
                private string $requestBody;
                private array  $requestHeaders = [];

                public function __construct(int $code, string $responseBody, array $responseHeaders, string $requestUrl, string $requestMethod, string $requestBody, array $requestHeaders)
                {
                    parent::__construct('Received ' . $code, $code);
            
                    $this->responseBody    = $responseBody;
                    $this->responseHeaders = $responseHeaders;
                    $this->requestUrl      = $requestUrl;
                    $this->requestMethod   = $requestMethod;
                    $this->requestBody     = $requestBody;
                    $this->requestHeaders  = $requestHeaders;
                }
            
                public function getResponseBody(): string
                {
                    return $this->responseBody;
                }
            
                public function getResponseDecoded()
                {
                    return json_decode($this->getResponseBody(), true);
                }
            
                public function getResponseHeaders(): array
                {
                    return $this->responseHeaders;
                }

                public function getRequestUrl(): string
                {
                    return $this->requestUrl;
                }

                public function getRequestMethod(): string
                {
                    return $this->requestMethod;
                }

                public function getRequestBody(): string
                {
                    return $this->requestBody;
                }

                public function getRequestHeaders(): array
                {
                    return $this->requestHeaders;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Exception/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Exception\RequestException', $className);
    }

    private function getSut(): RequestExceptionTemplate
    {
        return new RequestExceptionTemplate($this->locationHelper);
    }
}
