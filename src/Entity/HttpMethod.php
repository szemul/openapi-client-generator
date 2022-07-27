<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

use Emul\Enum\EnumAbstract;

class HttpMethod extends EnumAbstract
{
    public const GET    = 'GET';
    public const POST   = 'POST';
    public const DELETE = 'DELETE';
    public const PATCH  = 'PATCH';
    public const PUT    = 'PUT';

    public static function get(): self
    {
        return new self(self::GET);
    }

    public static function post(): self
    {
        return new self(self::POST);
    }

    public static function delete(): self
    {
        return new self(self::DELETE);
    }

    public static function patch(): self
    {
        return new self(self::PATCH);
    }

    public static function put(): self
    {
        return new self(self::PUT);
    }

    protected static function getPossibleValues(): array
    {
        return [
            self::GET,
            self::POST,
            self::DELETE,
            self::PATCH,
            self::PUT,
        ];
    }
}
