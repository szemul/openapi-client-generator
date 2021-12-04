<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterIn;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ApiActionTemplate extends TemplateAbstract
{
    private const RESERVED_VARIABLE_NAMES = [
        'request',
        'psrRequest',
        'headers',
        'endpointPath',
        'response',
        'responseCode',
        'responseBody',
        'responseHeaders',
        'queryString',
    ];

    private string     $operationId;
    private ?string    $requestModelClassName;
    private string     $url;
    private HttpMethod $httpMethod;
    /** @var Parameter[] */
    private array $parameters;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $operationId,
        ?string $requestModelClassName,
        string $url,
        HttpMethod $httpMethod,
        Parameter ...$parameters
    ) {
        parent::__construct($locationHelper, $stringHelper);

        // TODO figure out if lcfirst($operationId) would make sense, or don't mess with the incoming operation id
        $this->operationId           = $operationId;
        $this->requestModelClassName = $requestModelClassName;
        $this->url                   = $url;
        $this->httpMethod            = $httpMethod;
        $this->parameters            = $parameters;
    }

    public function __toString(): string
    {
        // TODO it would be nice to escape the URL and other user inputs...
        return <<<ACTION
            public function {$this->operationId}({$this->getParameters()}): void
            {
                \$headers = array_merge(
                    \$this->defaultHeaders,
                    [
                        'Content-Type'                              => 'application/json',
                        \$this->configuration->getApiKeyHeaderName() => \$this->configuration->getApiKey(),
                    ],
                );
                
                \$endpointPath = '{$this->url}';
                
                {$this->getEndpointPathReplacements()}
                
                {$this->getQuery()}

                \$psrRequest = \$this->requestFactory->createRequest(
                    '{$this->httpMethod->__toString()}',
                    \$this->configuration->getHost() . \$endpointPath . \$queryString
                );

                foreach (\$headers as \$name => \$value) {
                    \$psrRequest->withHeader(\$name, \$value);
                }

                {$this->setRequestToBody()}
                \$response     = \$this->httpClient->sendRequest(\$psrRequest);
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
        $modelFullClassNames = [];
        if (null !== $this->requestModelClassName) {
            $modelFullClassNames[] = $this->getLocationHelper()->getModelNamespace() . '\\' . $this->requestModelClassName;
        }

        foreach ($this->parameters as $parameter) {
            if ($parameter->getType()->isScalar()) {
                continue;
            }

            $modelFullClassNames[] = $parameter->getType()->getObjectClassname();
        }

        return $modelFullClassNames;
    }

    private function getParameters(): string
    {
        $parameters = [];

        if (null !== $this->requestModelClassName) {
            $parameters[] = "{$this->requestModelClassName} \$request";
        }

        foreach ($this->parameters as $parameter) {
            $parameters[] = ($parameter->isRequired() ? '' : '?') . $parameter->getType()->getPhpType() . ' '
                . $this->getNameForParameter($parameter) . ($parameter->isRequired() ? '' : ' = null');
        }

        return implode(', ', $parameters);
    }

    private function setRequestToBody(): string
    {
        if (null === $this->requestModelClassName) {
            return '';
        }

        return '$psrRequest->withBody($this->streamFactory->createStream(json_encode($request)));';
    }

    private function getNameForParameter(Parameter $parameter): string
    {
        return '$' . $parameter->getName()
            . (in_array($parameter->getName(), self::RESERVED_VARIABLE_NAMES) ? 'Parameter' : '');
    }

    private function getEndpointPathReplacements(): string
    {
        $needles      = [];
        $replacements = [];

        foreach ($this->parameters as $parameter) {
            if (!$parameter->getIn()->isEqualToString(ParameterIn::PATH)) {
                continue;
            }

            $needles[]      = '\'{' . $parameter->getName() . '}\'';
            $replacements[] = "(string){$this->getNameForParameter($parameter)}";
        }

        if (empty($needles)) {
            return '';
        }

        return '$endpointPath = str_replace(
            [
                ' . implode(",\n                ", $needles) . '
            ], 
            [
                ' . implode(",\n                ", $replacements) . '
            ], 
            $endpointPath
            );';
    }

    private function getQuery(): string
    {
        $queryParts = [];

        foreach ($this->parameters as $parameter) {
            if (!$parameter->getIn()->isEqualToString(ParameterIn::QUERY)) {
                continue;
            }

            $queryParts[] = "'{$parameter->getName()}' => (string){$this->getNameForParameter($parameter)}";
        }

        if (empty($queryParts)) {
            return "\$queryString = '';";
        }

        return '$queryString = \'?\'. http_build_query([
            ' . implode(",\n            ", $queryParts) . '
        ]);';
    }
}
