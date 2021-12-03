<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ApiActionTemplate extends TemplateAbstract
{
    private string     $operationId;
    private string     $requestModelClassName;
    private string     $url;
    private HttpMethod $httpMethod;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $operationId,
        string $requestModelClassName,
        string $url,
        HttpMethod $httpMethod
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->operationId           = $operationId;
        $this->requestModelClassName = $requestModelClassName;
        $this->url                   = $url;
        $this->httpMethod            = $httpMethod;
    }

    public function __toString(): string
    {
        return <<<ACTION
            public function {$this->operationId}({$this->requestModelClassName} \$request): void
            {
                \$payload = json_encode(\$request);
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        \$this->configuration->getApiKeyHeaderName() => \$this->configuration->getApiKey(),
                    ],
                );

                \$request = \$this->requestFactory->createRequest(
                    '{$this->httpMethod->__toString()}',
                    \$this->configuration->getHost() . '{$this->url}'
                );

                foreach (\$headers as \$name => \$value) {
                    \$request->withHeader(\$name, \$value);
                }
                \$request->withBody(\$this->streamFactory->createStream(\$payload));

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
                    //TODO: Return Response
                }
            }
            ACTION;
    }

    public function getModelFullClassNames(): array
    {
        return [
            $this->getLocationHelper()->getModelNamespace() . '\\' . $this->requestModelClassName,
        ];
    }
}
