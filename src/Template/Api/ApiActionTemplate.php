<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\ExceptionClass;
use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\ResponseClass;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

class ApiActionTemplate
{
    private string $actionName;

    /** @var ResponseClass[] */
    private array $responseClasses = [];

    /** @var ExceptionClass[] */
    private array $exceptionClasses = [];

    /**
     * @param ResponseClass[] $responseClasses
     * @param ExceptionClass[] $exceptionClasses
     */
    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper,
        string $operationId,
        private readonly string $parameterClassName,
        private readonly string $url,
        private readonly HttpMethod $httpMethod,
        array $responseClasses,
        array $exceptionClasses
    ) {
        $this->actionName       = $this->stringHelper->convertToMethodOrVariableName($operationId);
        $this->responseClasses  = $responseClasses;
        $this->exceptionClasses = $exceptionClasses;
    }

    public function __toString(): string
    {
        $returnType          = $this->getReturnType();
        $responseHandling    = $this->getResponseHandling();
        $returnDocumentation = $this->getReturnDocumentation();
        $throwsDocumentation = $this->getThrowsDocumentation();
        $documentationLines  = array_merge($returnDocumentation, $throwsDocumentation);
        $documentationLines  = empty($documentationLines) ? ['*'] : $documentationLines;
        $documentation       = implode(PHP_EOL . ' ', $documentationLines);

        return <<<ACTION
            /**
             {$documentation}
             */
            public function {$this->actionName}({$this->parameterClassName} \$request, ?string \$overwriteUrl = null): {$returnType}
            {
                \$path    = '{$this->url}';
                \$payload = \$request->hasRequestModel() ? json_encode(\$request->getRequestModel()) : '';
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ],
                );
                
                if (!empty(\$this->configuration->getApiKeyHeaderName())) {
                    \$headers[\$this->configuration->getApiKeyHeaderName()] = \$this->configuration->getApiKey();
                }

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
                    
                \$fullUrl = \$overwriteUrl ?? (\$this->configuration->getHost() . \$path);
                \$request = \$this->requestFactory->createRequest('{$this->httpMethod->__toString()}', \$fullUrl);

                foreach (\$headers as \$name => \$value) {
                    \$request = \$request->withHeader(\$name, \$value);
                }
                \$request = \$request->withBody(\$this->streamFactory->createStream(\$payload));

                \$response     = \$this->httpClient->sendRequest(\$request);
                \$responseCode = \$response->getStatusCode();
                \$responseBody = \$response->getBody()->getContents();

                if (\$responseCode >= 400) {
                    \$requestExceptionClass = '\\{$this->locationHelper->getExceptionNamespace()}\Request' . \$responseCode . 'Exception';
                    \$responseHeaders       = \$response->getHeaders();

                    if (class_exists(\$requestExceptionClass)) {
                        throw new \$requestExceptionClass(\$responseBody, \$responseHeaders, \$fullUrl, '{$this->httpMethod->__toString()}', \$payload, \$headers);
                    } else {
                        throw new RequestException(\$responseCode, \$responseBody, \$responseHeaders, \$fullUrl, '{$this->httpMethod->__toString()}', \$payload, \$headers);
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
        return $this->locationHelper->getActionParameterNamespace() . '\\' . $this->parameterClassName;
    }

    public function getClassesToImport(): array
    {
        $result = [
            $this->locationHelper->getRootNamespace() . '\\ArrayMapperFactory',
            $this->locationHelper->getModelNamespace() . '\\GeneralResponse',
            $this->locationHelper->getModelNamespace() . '\\ResponseInterface',
        ];

        foreach ($this->responseClasses as $responseClass) {
            $result[] = $this->locationHelper->getModelNamespace() . '\\' . $responseClass->getModelClassName();
        }

        foreach ($this->exceptionClasses as $exceptionClass) {
            $result[] = $this->locationHelper->getExceptionNamespace() . '\\' . $exceptionClass->getClassName();
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
        return $this->stringHelper->convertToMethodOrVariableName("get_{$this->actionName}_Response_{$statusCode}");
    }

    private function getReturnDocumentation(): array
    {
        $documentationLines = [];
        foreach ($this->responseClasses as $responseClass) {
            $statusCode        = $responseClass->getStatusCode();
            $responseClassName = $responseClass->getModelClassName();

            $documentationLines[] = '* @return ' . $responseClassName . ' => ' . $statusCode;
        }
        $documentationLines[] = '* @return GeneralResponse => default';

        return $documentationLines;
    }

    private function getThrowsDocumentation(): array
    {
        $documentationLines = [];

        foreach ($this->exceptionClasses as $exceptionClass) {
            $documentationLines[] = "* @throws {$exceptionClass->getClassName()} when received {$exceptionClass->getStatusCode()} ({$exceptionClass->getDescription()})";
        }

        return $documentationLines;
    }

    private function getReturnType(): string
    {
        $returnTypes = [];
        foreach ($this->responseClasses as $responseClass) {
            $responseClassName = $responseClass->getModelClassName();

            $returnTypes[] = $responseClassName;
        }
        $returnTypes[] = 'GeneralResponse';

        return implode('|', array_unique($returnTypes));
    }

    private function getResponseHandling(): string
    {
        $responseHandlerMatch = '';
        foreach ($this->responseClasses as $responseClass) {
            $statusCode = $responseClass->getStatusCode();

            $responseHandlerMethodName = $this->getResponseGetterMethodName($statusCode);
            $responseHandlerMatch .= "{$statusCode} => \$this->{$responseHandlerMethodName}(\$responseCode, \$responseBody)," . PHP_EOL;
        }
        $responseHandlerMatch .= "default => \$this->{$this->getResponseGetterMethodName(null)}(\$responseCode, \$responseBody)," . PHP_EOL;

        return <<<RESPONSE_HANDLING
            return match (\$responseCode) {
                $responseHandlerMatch
            };
            RESPONSE_HANDLING;
    }
}
