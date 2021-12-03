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
        $this->expectModelClassNamesRetrievedFromAction('Model');
        $this->expectActionRendered('Action');

        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Api\Api;
            
            use Psr\Http\Client\ClientInterface;
            use Psr\Http\Message\RequestFactoryInterface;
            use Psr\Http\Message\StreamFactoryInterface;
            use Api\Configuration;
            use Api\Exception\RequestException;
            use Model;
            
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
            
            }
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    private function expectModelClassNamesRetrievedFromAction(string $modelClassName): void
    {
        $this->action
            ->shouldReceive('getModelFullClassNames')
            ->once()
            ->andReturn([$modelClassName]);
    }

    private function expectActionRendered(string $expectedResult): void
    {
        $this->action
            ->shouldReceive('__toString')
            ->once()
            ->andReturn($expectedResult);
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
