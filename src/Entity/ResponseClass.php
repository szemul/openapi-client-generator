<?php

namespace Emul\OpenApiClientGenerator\Entity;

class ResponseClass
{
    public function __construct(
        private readonly int $statusCode,
        private readonly bool $isList,
        private readonly string $modelClassName
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isList(): bool
    {
        return $this->isList;
    }

    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }
}
