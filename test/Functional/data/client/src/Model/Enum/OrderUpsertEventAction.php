<?php
declare(strict_types=1);

namespace Test\Model\Enum;

use Emul\Enum\EnumAbstract;

class OrderUpsertEventAction extends EnumAbstract
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const CANCEL = 'cancel';

    public static function create(): self
    {
        return new self(self::CREATE);
    }

    public static function update(): self
    {
        return new self(self::UPDATE);
    }

    public static function cancel(): self
    {
        return new self(self::CANCEL);
    }

    protected static function getPossibleValues(): array
    {
        return [self::CREATE, self::UPDATE, self::CANCEL];
    }
}
