<?php
declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class Parameter
{

    private string       $name;
    private ParameterIn  $in;
    private bool         $required;
    private PropertyType $type;

    public function __construct(string $name, ParameterIn $in, bool $required, PropertyType $type)
    {
        $this->name     = $name;
        $this->in       = $in;
        $this->required = $required;
        $this->type     = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIn(): ParameterIn
    {
        return $this->in;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }
}
