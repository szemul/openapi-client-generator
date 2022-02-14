<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ApiActionTemplate extends TemplateAbstract
{
    private string     $actionName;
    private string     $parameterClassName;
    private string     $url;
    private HttpMethod $httpMethod;
    private ?bool      $responseIsList    = null;
    private ?string    $responseClassName = null;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $operationId,
        string $parameterClassName,
        string $url,
        HttpMethod $httpMethod,
        ?bool $responseIsList,
        ?string $responseClassName
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->actionName         = $this->getStringHelper()->convertToMethodOrVariableName($operationId);
        $this->parameterClassName = $parameterClassName;
        $this->url                = $url;
        $this->httpMethod         = $httpMethod;
        $this->responseIsList     = $responseIsList;
        $this->responseClassName  = $responseClassName;
    }

    public function __toString(): string
    {
        if (empty($this->responseClassName)) {
            $returnType       = 'string';
            $responseHandling = <<<'RESPONSE'
                return $response->getBody()->getContents();
                RESPONSE;
        } else {
            $returnType = $this->responseClassName;

            if ($this->responseIsList) {
                $responseHandling = <<<RESPONSE
                    \$mapper = (new ArrayMapperFactory())->getMapper();
                    \$list   = new {$this->responseClassName}();

                    foreach (json_decode(\$response->getBody()->getContents(), true) as \$item) {
                        \$list->add(\$mapper->map(\$item, \$list->getItemClass()));
                    }
                    RESPONSE;
            } else {
                $responseHandling = <<<RESPONSE
                return (new ArrayMapperFactory())
                    ->getMapper()
                    ->map(
                        json_decode(\$response->getBody()->getContents(), true),
                        {$this->responseClassName}::class
                    );
                RESPONSE;
            }
        }

        return <<<ACTION
            public function {$this->actionName}({$this->parameterClassName} \$request): {$returnType}
            {
                \$path    = '{$this->url}';
                \$payload = json_encode(\$request->getRequestModel());
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
                    '{$this->httpMethod->__toString()}',
                    \$this->configuration->getHost() . \$path,
                );

                foreach (\$headers as \$name => \$value) {
                    \$request = \$request->withHeader(\$name, \$value);
                }
                \$request = \$request->withBody(\$this->streamFactory->createStream(\$payload));

                \$response     = \$this->httpClient->sendRequest(\$request);
                \$responseCode = \$response->getStatusCode();

                if (\$responseCode >= 400) {
                    \$requestExceptionClass = '\\{$this->getLocationHelper()->getExceptionNamespace()}\Request' . \$responseCode . 'Exception';
                    \$responseBody          = \$response->getBody()->getContents();
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

    public function getParameterFullClassName(): string
    {
        return $this->getLocationHelper()->getActionParameterNamespace() . '\\' . $this->parameterClassName;
    }

    public function getClassesToImport(): array
    {
        $result = [];

        if (!empty($this->responseClassName)) {
            $result[] = $this->getLocationHelper()->getRootNamespace() . '\\ArrayMapperFactory';
            $result[] = $this->getLocationHelper()->getModelNamespace() . '\\' . $this->responseClassName;
        }

        return $result;
    }
}
