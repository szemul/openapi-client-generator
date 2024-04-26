<?php
declare(strict_types=1);

namespace Test\Exception;

class Request404Exception extends RequestException
{
    public function __construct(string $responseBody, array $responseHeaders, string $requestUrl, string $requestMethod, string $requestBody, array $requestHeaders)
    {
        parent::__construct(404, $responseBody, $responseHeaders, $requestUrl, $requestMethod, $requestBody, $requestHeaders);
    }

    /**
     * @return string|null   The code of the error
     */
    public function getErrorCode(): ?string
    {
        return $this->getResponseDecoded()['errorCode'] ?? null;
    }

    /**
     * @return string|null   Description of the error
     */
    public function getErrorMessage(): ?string
    {
        return $this->getResponseDecoded()['errorMessage'] ?? null;
    }

    /**
     * @return array|null   List of the invalid params where the property is the parameter name and the value is the describing the issue
     */
    public function getParams(): ?array
    {
        return $this->getResponseDecoded()['params'] ?? null;
    }
}
