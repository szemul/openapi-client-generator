<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Emul\OpenApiClientGenerator\Template\RequestCodeExceptionTemplate;
use Emul\OpenApiClientGenerator\Template\ErrorPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Mockery;

class RequestCodeExceptionTemplateTest extends TestCaseAbstract
{
    private string $rootNamespace = 'Root';
    private int    $errorCode     = 400;

    public function testToString_shouldRenderException()
    {
        $property1 = $this->expectErrorPropertyUsed('public function getOne(){}');
        $property2 = $this->expectErrorPropertyUsed('public function getTwo(){}');

        $result = (string)$this->getSut($property1, $property2);
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root;
            
            class Request400Exception extends RequestException
            {
                public function __construct(string $responseBody, array $responseHeaders)
                {
                    parent::__construct(400, $responseBody, $responseHeaders);
                }
            
            public function getOne(){}
            public function getTwo(){}
            
            }
            EXPECTED;
        $this->assertSame($expectedResult, $result);
    }

    private function expectErrorPropertyUsed(string $getter): ErrorPropertyTemplate
    {
        return Mockery::mock(ErrorPropertyTemplate::class)
            ->shouldReceive('getGetter')
            ->andReturn($getter)
            ->getMock();
    }

    private function getSut(ErrorPropertyTemplate ...$properties): RequestCodeExceptionTemplate
    {
        return new RequestCodeExceptionTemplate($this->rootNamespace, $this->errorCode, ...$properties);
    }
}
