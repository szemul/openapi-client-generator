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
        $result                 = (string)$this->getSut(null, null);
        $expectedReturnType     = 'string';
        $expectedResultHandling = 'return $response->getBody()->getContents();';

        $this->assertActionSame($expectedReturnType, $expectedResultHandling, $result);
    }

    public function testToStringWhenResponseClassGiven_shouldGenerateTemplate()
    {
        $responseClassName = 'ResponseClass';
        $result            = (string)$this->getSut(false, $responseClassName);

        $expectedReturnType     = $responseClassName;
        $expectedResultHandling = <<<'RESPONSE'
            return (new ArrayMapperFactory())
                ->getMapper()
                ->map(
                    json_decode($response->getBody()->getContents(), true),
                    ResponseClass::class
                );
            RESPONSE;

        $this->assertActionSame($expectedReturnType, $expectedResultHandling, $result);
    }

    public function testToStringWhenResponseListClassGiven_shouldGenerateTemplate()
    {
        $responseClassName = 'ResponseListClass';
        $result            = (string)$this->getSut(true, $responseClassName);

        $expectedResultHandling = <<<'RESPONSE'
            $mapper = (new ArrayMapperFactory())->getMapper();
            $list   = new ResponseListClass();
            
            foreach (json_decode($response->getBody()->getContents(), true) as $item) {
                $list->add($mapper->map($item, $list->getItemClass()));
            }
            RESPONSE;

        $this->assertActionSame($responseClassName, $expectedResultHandling, $result);
    }

    private function assertActionSame(string $expectedReturnType, string $expectedResponseHandling, string $result)
    {
        $expectedResult = <<<EXPECTED
            public function createEntity(EntityCreateRequest \$request): $expectedReturnType
            {
                \$path    = '/entity';
                \$payload = json_encode(\$request);
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        \$this->configuration->getApiKeyHeaderName() => \$this->configuration->getApiKey(),
                    ],
                );
            
                foreach (\$request->getHeaderParameterGetters() as \$parameterName => \$getterName) {
                    \$headers[\$parameterName] = \$request->\$getterName();
                }
            
                foreach (\$request->getPathParameterGetters() as \$parameterName => \$getterName) {
                    \$path = str_replace('{' . \$parameterName . '}', \$request->\$getterName(), \$path);
                }

                \$queryParameters = [];
                foreach (\$request->getQueryParameterGetters() as \$parameterName => \$getterName) {
                    \$queryParameters[\$parameterName] = \$request->\$getterName();
                }
            
                \$path .= strpos(\$path, '?') === false
                    ? '?' . http_build_query(\$queryParameters)
                    : '&' . http_build_query(\$queryParameters);
            
                \$request = \$this->requestFactory->createRequest(
                    'POST',
                    \$this->configuration->getHost() . \$path,
                );
            
                foreach (\$headers as \$name => \$value) {
                    \$request = \$request->withHeader(\$name, \$value);
                }
                \$request = \$request->withBody(\$this->streamFactory->createStream(\$payload));
            
                \$response     = \$this->httpClient->sendRequest(\$request);
                \$responseCode = \$response->getStatusCode();
            
                if (\$responseCode >= 400) {
                    \$requestExceptionClass = 'Request' . \$responseCode . 'Exception';
                    \$responseBody          = \$response->getBody()->getContents();
                    \$responseHeaders       = \$response->getHeaders();
            
                    if (class_exists(\$requestExceptionClass)) {
                        throw new \$requestExceptionClass(\$responseCode, \$responseBody, \$responseHeaders);
                    } else {
                        throw new RequestException(\$responseCode, \$responseBody, \$responseHeaders);
                    }
                } else {
                    $expectedResponseHandling
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    private function getSut(?bool $responseIsList, ?string $responseClassName): ApiActionTemplate
    {
        return new ApiActionTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->operationId,
            $this->requestModelClassName,
            $this->url,
            $this->httpMethod,
            $responseIsList,
            $responseClassName
        );
    }
}
