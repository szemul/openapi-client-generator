<?php
declare(strict_types=1);

namespace Test\Model\Enum;

use Emul\Enum\EnumAbstract;

class GetOrderListSort extends EnumAbstract
{
    public const ORDER_ID   = 'orderId';
    public const CREATED_AT = 'createdAt';

    public static function orderId(): self
    {
        return new self(self::ORDER_ID);
    }

    public static function createdAt(): self
    {
        return new self(self::CREATED_AT);
    }

    protected static function getPossibleValues(): array
    {
        return [self::ORDER_ID, self::CREATED_AT];
    }
}
