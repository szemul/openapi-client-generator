<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly ?string $description,
        private readonly ?string $schemaName
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSchemaName(): ?string
    {
        return $this->schemaName;
    }
}
