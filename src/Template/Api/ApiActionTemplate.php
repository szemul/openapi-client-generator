<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\ResponseClass;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ApiActionTemplate extends TemplateAbstract
{
    private string $actionName;

    /** @var ResponseClass[] */
    private array $responseClasses = [];

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $operationId,
        private readonly string $parameterClassName,
        private readonly string $url,
        private readonly HttpMethod $httpMethod,
        ResponseClass ...$responseClasses
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->actionName      = $this->getStringHelper()->convertToMethodOrVariableName($operationId);
        $this->responseClasses = $responseClasses;
    }

    public function __toString(): string
    {
        $responseHandlerMatch = '';
        $returnTypes          = [];
        $returnDocumentation  = '';

        foreach ($this->responseClasses as $responseClass) {
            $statusCode        = $responseClass->getStatusCode();
            $responseClassName = $responseClass->getModelClassName();

            $returnDocumentation .= '* @return ' . $responseClassName . ' => ' . $statusCode . PHP_EOL;
            $returnTypes[]             = $responseClassName;
            $responseHandlerMethodName = $this->getResponseGetterMethodName($statusCode);
            $responseHandlerMatch .= "{$statusCode} => \$this->{$responseHandlerMethodName}(\$responseCode, \$responseBody)," . PHP_EOL;
        }
        $responseHandlerMatch .= "default => \$this->{$this->getResponseGetterMethodName(null)}(\$responseCode, \$responseBody)," . PHP_EOL;
        $returnType           = implode('|', array_unique($returnTypes));

        $responseHandling = <<<RESPONSE_HANDLING
            return match (\$responseCode) {
                $responseHandlerMatch
            };
            RESPONSE_HANDLING;

        return <<<ACTION
            /**
             {$returnDocumentation}
             */
            public function {$this->actionName}({$this->parameterClassName} \$request): {$returnType}
            {
                \$path    = '{$this->url}';
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
                    '{$this->httpMethod->__toString()}',
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
                    \$requestExceptionClass = '\\{$this->getLocationHelper()->getExceptionNamespace()}\Request' . \$responseCode . 'Exception';
                    \$responseHeaders       = \$response->getHeaders();

                    if (class_exists(\$requestExceptionClass)) {
                        throw new \$requestExceptionClass(\$responseBody, \$responseHeaders);
                    } else {
                        throw new RequestException(\$responseCode, \$responseBody, \$responseHeaders);
                    }
                } else {
                    {$responseHandling}
                }
            }
            ACTION;
    }

    public function getResponseHandlerMethods(): array
    {
        $responseHandlerMethods = [];
        foreach ($this->responseClasses as $responseClass) {
            $responseHandlerMethods[] = $this->generateResponseHandlerMethod($responseClass);
        }

        $responseHandlerMethods[] = $this->getGeneralResponseHandlerMethod();

        return $responseHandlerMethods;
    }

    public function getParameterFullClassName(): string
    {
        return $this->getLocationHelper()->getActionParameterNamespace() . '\\' . $this->parameterClassName;
    }

    public function getClassesToImport(): array
    {
        $result = [
            $this->getLocationHelper()->getRootNamespace() . '\\ArrayMapperFactory',
            $this->getLocationHelper()->getModelNamespace() . '\\GeneralResponse',
            $this->getLocationHelper()->getModelNamespace() . '\\ResponseInterface',
        ];

        foreach ($this->responseClasses as $responseClass) {
            $result[] = $this->getLocationHelper()->getModelNamespace() . '\\' . $responseClass->getModelClassName();
        }

        return $result;
    }

    private function generateResponseHandlerMethod(ResponseClass $responseClass): string
    {
        $methodName = $this->getResponseGetterMethodName($responseClass->getStatusCode());

        if ($responseClass->isList()) {
            $responseHandling = <<<RESPONSE
                    \$mapper = (new ArrayMapperFactory())->getMapper();
                    \$list   = (new {$responseClass->getModelClassName()}())
                        ->setStatusCode(\$statusCode)
                        ->setBody(\$responseBody);

                    foreach (json_decode(\$responseBody, true) as \$item) {
                        \$list->add(\$mapper->map(\$item, \$list->getItemClass()));
                    }

                    return \$list;
                    RESPONSE;
        } else {
            $responseHandling = <<<RESPONSE
                    \$response = (new ArrayMapperFactory())
                        ->getMapper()
                        ->map(
                            empty(\$responseBody) ? [] : json_decode(\$responseBody, true),
                            {$responseClass->getModelClassName()}::class
                        );
                    \$response
                        ->setStatusCode(\$statusCode)
                        ->setBody(\$responseBody);

                    return \$response;
                    RESPONSE;
        }

        return <<<METHOD
            private function {$methodName}(int \$statusCode, string \$responseBody): {$responseClass->getModelClassName()} 
            {
                $responseHandling
            }
            METHOD;
    }

    private function getGeneralResponseHandlerMethod(): string
    {
        $methodName = $this->getResponseGetterMethodName(null);

        return <<<METHOD
            private function {$methodName}(int \$statusCode, string \$responseBody): GeneralResponse 
            {
                return (new GeneralResponse)
                    ->setStatusCode(\$statusCode)
                    ->setBody(\$responseBody);
            }
            METHOD;
    }

    private function getResponseGetterMethodName(?int $statusCode): string
    {
        return $this->getStringHelper()->convertToMethodOrVariableName("get_{$this->actionName}_Response_{$statusCode}");
    }
}
