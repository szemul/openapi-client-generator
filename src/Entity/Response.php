<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class Response
{
    private int     $statusCode;
    private ?string $description = null;
    private ?string $schemaName  = null;

    public function __construct(int $statusCode, ?string $description, ?string $schemaName)
    {
        $this->statusCode  = $statusCode;
        $this->description = $description;
        $this->schemaName  = $schemaName;
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
