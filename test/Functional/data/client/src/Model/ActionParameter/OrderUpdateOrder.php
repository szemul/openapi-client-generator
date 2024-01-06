<?php
declare(strict_types=1);

namespace Test\Model\ActionParameter;

class OrderUpdateOrder
{
    public function __construct()
    {

    }

    public function getPathParameterGetters(): array
    {
        return [];
    }

    public function getQueryParameterGetters(): array
    {
        return [];
    }

    public function getHeaderParameterGetters(): array
    {
        return [];
    }

    public function hasRequestModel(): bool
    {
        return false;
    }
}
