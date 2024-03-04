<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

class PropertyType
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

    private function __construct(
        private readonly string $value,
        private readonly ?self $arrayItemType = null,
        private readonly ?string $objectClassname = null
    ) {
    }

    public function isScalar(): bool
    {
        return in_array((string)$this, self::SCALAR_TYPES);
    }

    public function getArrayItemType(): ?self
    {
        return $this->arrayItemType;
    }

    public function getArrayItemTypeString(): ?string
    {
        if (empty($this->getArrayItemType())) {
            return null;
        } elseif ($this->getArrayItemType()->isScalar()) {
            return (string)$this->getArrayItemType();
        } else {
            return '\\' . $this->getArrayItemType()->getObjectClassname();
        }
    }

    public function getObjectClassname($fqcn = true): ?string
    {
        return $fqcn
            ? $this->objectClassname
            : substr($this->objectClassname, strrpos($this->objectClassname, '\\') + 1);
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

    public static function array(?self $arrayItemType): self
    {
        return new self(self::ARRAY, $arrayItemType);
    }

    public static function object(string $objectClassname): self
    {
        return new self(self::OBJECT, null, $objectClassname);
    }
}
