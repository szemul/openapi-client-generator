<?php
declare(strict_types=1);

namespace Test\Model;

class OrderList extends ModelAbstract
{
    /**
     * @var \Test\Model\Order[]
     */
    protected array $Orders;

    public function __construct()
    {

    }

    /**
     * @return \Test\Model\Order[]
     */
    public function getOrders(): array
    {
        return $this->Orders;
    }

    public function setOrders(\Test\Model\Order ...$orders): self
    {
        $this->Orders = $orders;

        return $this;
    }
}
