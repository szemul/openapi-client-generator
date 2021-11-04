<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Example;

use Carbon\CarbonInterface;

class ExampleInnerRequestObject extends ModelAbstract
{
    protected string $key;
    protected string $value;

    public function __construct(string $key, string $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    public function getKey(bool $throwExceptionIfNotInitialized = false): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
