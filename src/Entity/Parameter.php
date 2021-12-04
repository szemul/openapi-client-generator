<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class Parameter
{
    private string        $name;
    private ParameterType $type;
    private ?string       $description;
    private bool          $isRequired;
    private PropertyType  $valueType;

    public function __construct(string $name, ParameterType $type, bool $isRequired, PropertyType $valueType, ?string $description)
    {
        $this->name        = $name;
        $this->type        = $type;
        $this->isRequired  = $isRequired;
        $this->valueType   = $valueType;
        $this->description = $description;
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

}
