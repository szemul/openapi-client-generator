<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\PropertyType;

use Exception;

class Type
{
    public const STRING = 'string';
    public const INT    = 'int';
    public const FLOAT  = 'float';
    public const BOOL   = 'bool';
    public const ARRAY  = 'array';
    public const OBJECT = 'object';

    public const SCALAR_TYPES = [
        self::STRING,
        self::INT,
        self::FLOAT,
        self::BOOL,
    ];

    private string  $value;
    private ?Type   $arrayItemType   = null;
    private ?string $objectClassname = null;

    private function __construct(string $value, ?Type $arrayItemType = null, ?string $objectClassname = null)
    {
        $this->value           = $value;
        $this->arrayItemType   = $arrayItemType;
        $this->objectClassname = $objectClassname;
    }

    public function isScalar(): bool
    {
        return in_array((string)$this, self::SCALAR_TYPES);
    }

    public function getArrayItemType(): ?Type
    {
        return $this->arrayItemType;
    }

    public function getObjectClassname(): ?string
    {
        return $this->objectClassname;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function string(): self
    {
        return new self(self::STRING);
    }

    public static function int(): self
    {
        return new self(self::INT);
    }

    public static function float(): self
    {
        return new self(self::FLOAT);
    }

    public static function bool(): self
    {
        return new self(self::BOOL);
    }

    public static function array(Type $arrayItemType): self
    {
        return new self(self::ARRAY, $arrayItemType);
    }

    public static function object(string $objectClassname): self
    {
        return new self(self::OBJECT, null, $objectClassname);
    }
}
