<?php

declare(strict_types=1);

namespace Test\Model;

interface ResponseInterface
{
    public function setStatusCode(int $statusCode): self;

    public function getStatusCode(): int;

    public function setBody(string $body): self;

    public function getBody(): string;
}
