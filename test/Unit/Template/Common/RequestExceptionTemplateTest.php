<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Template\Common\RequestExceptionTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class RequestExceptionTemplateTest extends TestCaseAbstract
{
    private $rootNamespace = 'Root';

    public function testToString_shouldRenderProperly()
    {
        $sut = new RequestExceptionTemplate($this->rootNamespace);

        $result = (string)$sut;
        $expectedResult = <<<'EXCEPTION'
            <?php
            declare(strict_types=1);
            
            namespace Root;
            
            use Carbon\CarbonInterface;
            use Exception;
            
            class RequestException extends Exception
            {
                private string $responseBody;
                private array  $responseHeaders = [];
            
                public function __construct(int $code, string $responseBody, array $responseHeaders)
                {
                    parent::__construct('Received ' . $code, $code);
            
                    $this->responseBody    = $responseBody;
                    $this->responseHeaders = $responseHeaders;
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
            }
            EXCEPTION;

        $this->assertSame($expectedResult, $result);
    }
}
