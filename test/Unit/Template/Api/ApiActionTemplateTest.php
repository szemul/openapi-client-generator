<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ApiActionTemplateTest extends TemplateTestCaseAbstract
{
    private string     $operationId           = 'createEntity';
    private string     $requestModelClassName = 'EntityCreateRequest';
    private string     $url                   = '/entity';
    private HttpMethod $httpMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpMethod = HttpMethod::post();
    }

    public function testToString_shouldGenerateTemplate()
    {
        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            public function createEntity(EntityCreateRequest $request): void
            {
                $payload = json_encode($request);
                $headers = array_merge(
                    $this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        $this->configuration->getApiKeyHeaderName() => $this->configuration->getApiKey(),
                    ],
                );

                $request = $this->requestFactory->createRequest(
                    'POST',
                    $this->configuration->getHost() . '/entity'
                );

                foreach ($headers as $name => $value) {
                    $request->withHeader($name, $value);
                }
                $request->withBody($this->streamFactory->createStream($payload));

                $response     = $this->httpClient->sendRequest($request);
                $responseCode = $response->getStatusCode();

                if ($responseCode >= 400) {
                    $requestExceptionClass = 'Request' . $responseCode . 'Exception';
                    $responseBody          = $response->getBody()->getContents();
                    $responseHeaders       = $response->getHeaders();

                    if (class_exists($requestExceptionClass)) {
                        throw new $requestExceptionClass($responseCode, $responseBody, $responseHeaders);
                    } else {
                        throw new RequestException($responseCode, $responseBody, $responseHeaders);
                    }
                } else {
                    //TODO: Return Response
                }
            }
            EXPECTED;
     
        $this->assertSame($expectedResult, $result);
    }

    private function getSut(): ApiActionTemplate
    {
        return new ApiActionTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->operationId,
            $this->requestModelClassName,
            $this->url,
            $this->httpMethod
        );
    }
}
