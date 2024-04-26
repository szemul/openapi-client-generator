<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Exception;

use Emul\OpenApiClientGenerator\Template\Exception\RequestCodeExceptionTemplate;
use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;
use Mockery;

class RequestCodeExceptionTemplateTest extends TemplateTestCaseAbstract
{
    private int                              $errorCode = 400;
    private RequestExceptionPropertyTemplate $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = Mockery::mock(RequestExceptionPropertyTemplate::class);
    }

    public function testToString_shouldRenderProperly()
    {
        $this->expectGetterGenerated('getter');

        $expectedValue = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root\Exception;
            
            class Request400Exception extends RequestException
            {
                public function __construct(string $responseBody, array $responseHeaders, string $requestUrl, string $requestMethod, string $requestBody, array $requestHeaders)
                {
                    parent::__construct(400, $responseBody, $responseHeaders, $requestUrl, $requestMethod, $requestBody, $requestHeaders);
                }
            
            getter

            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedValue, (string)$this->getSut());
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Exception/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Exception\Request400Exception', $className);
    }

    private function expectGetterGenerated(string $expectedResult): void
    {
        $this->property
            ->shouldReceive('getGetter')
            ->once()
            ->andReturn($expectedResult);
    }

    private function getSut(): RequestCodeExceptionTemplate
    {
        return new RequestCodeExceptionTemplate($this->locationHelper, $this->classHelper, $this->errorCode, $this->property);
    }
}
