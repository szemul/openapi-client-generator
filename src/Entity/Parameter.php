<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class Parameter
{
    public function __construct(
        private readonly string $name,
        private readonly ParameterType $type,
        private readonly bool $isRequired,
        private readonly PropertyType $valueType,
        private readonly ?string $description
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ParameterType
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getValueType(): PropertyType
    {
        return $this->valueType;
    }

    public function getPhpValueType(): string
    {
        return ($this->isRequired ? '' : '?') . $this->valueType;
    }
}
