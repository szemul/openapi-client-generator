<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Api;

use Emul\OpenApiClientGenerator\Entity\ExceptionClass;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\ResponseClass;
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
        $responseClass = new ResponseClass(200, false, 'ResponseClass');
        $result        = (string)$this->getSut([$responseClass], []);

        $expectedReturnType    = $responseClass->getModelClassName();
        $expectedDocumentation = <<<'DOC'
            /**
             * @return ResponseClass => 200
             */
            DOC;

        $expectedResultHandling = <<<'RESPONSE'
            return match ($responseCode) {
                200     => $this->getCreateEntityResponse200($responseCode, $responseBody),
                default => $this->getCreateEntityResponse($responseCode, $responseBody),
            };
            RESPONSE;

        $this->assertActionSame($expectedDocumentation, $expectedReturnType, $expectedResultHandling, $result);
    }

    public function testToStringWhenResponseListClassGiven_shouldGenerateTemplate()
    {
        $responseClass = new ResponseClass(200, true, 'ResponseListClass');
        $result        = (string)$this->getSut([$responseClass], []);

        $expectedDocumentation = <<<'DOC'
            /**
             * @return ResponseListClass => 200
             */
            DOC;

        $expectedResultHandling = <<<'RESPONSE'
            return match ($responseCode) {
                200     => $this->getCreateEntityResponse200($responseCode, $responseBody),
                default => $this->getCreateEntityResponse($responseCode, $responseBody),
            };
            RESPONSE;

        $this->assertActionSame($expectedDocumentation, $responseClass->getModelClassName(), $expectedResultHandling, $result);
    }

    public function testToStringWhenExceptionsGiven_shouldGenerateThrowsDocumentation()
    {
        $exceptionClass1 = new ExceptionClass(400, 'Bad Request', 'Exception400');
        $exceptionClass2 = new ExceptionClass(404, 'Not found', 'Exception404');
        $result          = (string)$this->getSut([], [$exceptionClass1, $exceptionClass2]);

        $expectedDocumentation = <<<'DOC'
            /**
             * @throws Exception400 when received 400 (Bad Request)
             * @throws Exception404 when received 404 (Not found)
             */
            DOC;

        $expectedResultHandling = <<<'RESPONSE'
            return match ($responseCode) {
                default => $this->getCreateEntityResponse($responseCode, $responseBody),
            };
            RESPONSE;

        $this->assertActionSame($expectedDocumentation, '', $expectedResultHandling, $result);
    }

    public function testGetParameterFullClassName()
    {
        $result = $this->getSut([], [])->getParameterFullClassName();

        $this->assertSame('Root\Model\ActionParameter\EntityCreateRequest', $result);
    }

    public function testGetClasses_shouldReturnMapperAndResponse()
    {
        $responseClass1  = new ResponseClass(200, false, 'Response1');
        $responseClass2  = new ResponseClass(201, false, 'Response2');
        $exceptionClass1 = new ExceptionClass(400, 'Bad Request', 'Exception400');
        $exceptionClass2 = new ExceptionClass(404, 'Not found', 'Exception404');

        $sut = $this->getSut([$responseClass1, $responseClass2], [$exceptionClass1, $exceptionClass2]);

        $result         = $sut->getClassesToImport();
        $expectedResult = [
            'Root\ArrayMapperFactory',
            'Root\Model\GeneralResponse',
            'Root\Model\ResponseInterface',
            'Root\Model\Response1',
            'Root\Model\Response2',
            'Root\Exception\Exception400',
            'Root\Exception\Exception404',
        ];

        $this->assertSame($expectedResult, $result);
    }

    private function assertActionSame(string $expectedDocumentation, string $expectedReturnType, string $expectedResponseHandling, string $result)
    {
        $expectedResult = $expectedDocumentation . <<<EXPECTED

            public function createEntity(EntityCreateRequest \$request): $expectedReturnType
            {
                \$path    = '/entity';
                \$payload = \$request->hasRequestModel() ? json_encode(\$request->getRequestModel()) : '';
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        'Accept'                                    => 'application/json',
                        \$this->configuration->getApiKeyHeaderName() => \$this->configuration->getApiKey(),
                    ],
                );
            
                foreach (\$request->getHeaderParameterGetters() as \$parameterName => \$getterName) {
                    \$headers[\$parameterName] = \$request->\$getterName();
                }
            
                foreach (\$request->getPathParameterGetters() as \$parameterName => \$getterName) {
                    \$path = str_replace('{' . \$parameterName . '}', (string)\$request->\$getterName(), \$path);
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
                \$responseBody = \$response->getBody()->getContents();
            
                if (\$responseCode >= 400) {
                    \$requestExceptionClass = '\Root\Exception\Request' . \$responseCode . 'Exception';
                    \$responseHeaders       = \$response->getHeaders();
            
                    if (class_exists(\$requestExceptionClass)) {
                        throw new \$requestExceptionClass(\$responseBody, \$responseHeaders);
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

    /**
     * @param ResponseClass[] $responseClasses
     * @param ExceptionClass[] $exceptionClasses
     */
    private function getSut(array $responseClasses, array $exceptionClasses): ApiActionTemplate
    {
        return new ApiActionTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->operationId,
            $this->requestModelClassName,
            $this->url,
            $this->httpMethod,
            $responseClasses,
            $exceptionClasses
        );
    }
}
