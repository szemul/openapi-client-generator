<?php

declare(strict_types=1);

namespace Test\Model;

trait ResponseTrait
{
    private int $statusCode;
    private string $body;

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
