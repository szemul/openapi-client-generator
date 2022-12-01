<?php
declare(strict_types=1);

namespace Test\Exception;

use Exception;

class RequestException extends Exception
{
    private string $responseBody;
    private array  $responseHeaders = [];

    public function __construct(int $code, string $responseBody, array $responseHeaders)
    {
        parent::__construct('Received ' . $code, $code);

        $this->responseBody    = $responseBody;
        $this->responseHeaders = $responseHeaders;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getResponseDecoded()
    {
        return json_decode($this->getResponseBody(), true);
    }

    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }
}
