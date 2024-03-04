<?php
declare(strict_types=1);

namespace Test\Model\ActionParameter;

class OrderGetOrderList
{
    private \Test\Model\Enum\GetOrderListSort|null $querySort;

    public function __construct(\Test\Model\Enum\GetOrderListSort|null $sort = null)
    {
        $this->querySort = $sort;
    }

    public function getPathParameterGetters(): array
    {
        return [];
    }

    public function getQueryParameterGetters(): array
    {
        return ['sort' => 'getQuerySort'];
    }

    public function getHeaderParameterGetters(): array
    {
        return [];
    }

    public function getQuerySort(): ?string
    {
        return is_object($this->querySort) ? (string)$this->querySort : null;
    }

    public function setQuerySort(\Test\Model\Enum\GetOrderListSort|null $parameter): self
    {
        $this->querySort = $parameter;

        return $this;
    }

    public function hasRequestModel(): bool
    {
        return false;
    }
}
