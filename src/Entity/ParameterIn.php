<?php
declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Entity;

use Emul\Enum\EnumAbstract;

class ParameterIn extends EnumAbstract
{
    public const PATH  = 'path';
    public const QUERY = 'query';

    protected static function getPossibleValues(): array
    {
        return [
            self::PATH,
            self::QUERY,
        ];
    }
}
