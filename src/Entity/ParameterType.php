<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

use Emul\Enum\EnumAbstract;

class ParameterType extends EnumAbstract
{
    public const PATH   = 'path';
    public const QUERY  = 'query';
    public const HEADER = 'header';

    public static function path(): self
    {
        return new self(self::PATH);
    }

    public static function query(): self
    {
        return new self(self::QUERY);
    }

    public static function header(): self
    {
        return new self(self::HEADER);
    }

    protected static function getPossibleValues(): array
    {
        return [
            self::PATH,
            self::QUERY,
            self::HEADER,
        ];
    }
}
