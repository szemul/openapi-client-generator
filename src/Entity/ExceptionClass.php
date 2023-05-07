<?php

namespace Emul\OpenApiClientGenerator\Entity;

class ExceptionClass
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $description,
        private readonly string $className
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
