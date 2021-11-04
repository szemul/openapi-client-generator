<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

class ApiActionTemplate extends TemplateAbstract
{
    private string $operationId;
    private string $requestModelClassName;
    private string $url;

    public function __construct(string $rootNamespace, string $operationId, string $requestModelClassName, string $url)
    {
        parent::__construct($rootNamespace);

        $this->operationId           = $operationId;
        $this->requestModelClassName = $requestModelClassName;
        $this->url                   = $url;
    }

    public function __toString(): string
    {
        return <<<ACTION
            public function {$this->operationId}({$this->requestModelClassName} \$request): void
            {
                \$payload = Utils::jsonEncode(\$request);
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        \$this->configuration->getApiKeyHeaderName() => \$this->configuration->getApiKey(),
                    ]
                );
            
                \$guzzleRequest = new Request(
                    'POST',
                    \$this->configuration->getHost() . '{$this->url}',
                    \$headers,
                    \$payload
                );
            
                try {
                    \$response = \$this->httpClient->send(\$guzzleRequest);
                } catch (GuzzleRequestException \$exception) {
                    \$errorCode             = \$exception->getCode();
                    \$requestExceptionClass = 'Request' . \$errorCode . 'Exception';
                    \$responseBody          = \$exception->getResponse()->getBody()->getContents();
                    \$responseHeaders       = \$exception->getResponse()->getHeaders();
            
                    if (class_exists(\$requestExceptionClass)) {
                        throw new \$requestExceptionClass((int)\$errorCode, \$responseBody, \$responseHeaders);
                    } else {
                        throw new RequestException((int)\$errorCode, \$responseBody, \$responseHeaders);
                    }
                }
            }
            ACTION;
    }

    public function getModelFullClassNames(): array
    {
        return [
            $this->getModelNamespace() . '\\' . $this->requestModelClassName,
        ];
    }
}
