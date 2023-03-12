<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Api;

use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\ApiTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;
use Mockery;

class ApiTemplateTest extends TemplateTestCaseAbstract
{
    private string            $apiTag = 'Test';
    private ApiActionTemplate $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = Mockery::mock(ApiActionTemplate::class);
    }

    public function testToString_shouldGenerateTemplate()
    {
        $this->expectParameterFullClassNameRetrieved('ActionParameter');
        $this->expectClassesToImportRetrieved('Class1', 'Class2');
        $this->expectActionRendered('Action');
        $this->expectResponseHandlersRendered('ResponseHandlers');

        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Root\Api;
            
            use Psr\Http\Client\ClientInterface;
            use Psr\Http\Message\RequestFactoryInterface;
            use Psr\Http\Message\StreamFactoryInterface;
            use Root\Configuration;
            use Root\Exception\RequestException;
            use ActionParameter;
            use Class1;
            use Class2;
            
            class TestApi
            {
                private Configuration           $configuration;
                private ClientInterface         $httpClient;
                private RequestFactoryInterface $requestFactory;
                private StreamFactoryInterface  $streamFactory;
                private array                   $defaultHeaders = [];

                public function __construct(
                    Configuration $configuration,
                    ClientInterface $httpClient,
                    RequestFactoryInterface $requestFactory,
                    StreamFactoryInterface $streamFactory,
                    array $defaultHeaders = []
                ) {
                    $this->configuration  = $configuration;
                    $this->httpClient     = $httpClient;
                    $this->requestFactory = $requestFactory;
                    $this->streamFactory  = $streamFactory;
                    $this->defaultHeaders = $defaultHeaders;
                }
            
            Action
            ResponseHandlers
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    private function expectActionRendered(string $expectedResult): void
    {
        $this->action
            ->shouldReceive('__toString')
            ->once()
            ->andReturn($expectedResult);
    }

    private function expectParameterFullClassNameRetrieved(string $expectedResult): void
    {
        $this->action
            ->shouldReceive('getParameterFullClassName')
            ->once()
            ->andReturn($expectedResult);
    }

    private function expectClassesToImportRetrieved(string ...$expectedClasses): void
    {
        $this->action
            ->shouldReceive('getClassesToImport')
            ->once()
            ->andReturn($expectedClasses);
    }

    private function expectResponseHandlersRendered(string $responseHandler): void
    {
        $this->action
            ->expects('getResponseHandlerMethods')
            ->andReturn([$responseHandler]);
    }

    private function getSut(): ApiTemplate
    {
        return new ApiTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->apiTag,
            $this->action
        );
    }
}
