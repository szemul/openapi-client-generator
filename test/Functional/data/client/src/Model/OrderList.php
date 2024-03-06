<?php
declare(strict_types=1);

namespace Test\Model;

class OrderList extends ModelAbstract
{
    /**
     * @var \Test\Model\Order[]
     */
    protected ?array $Orders;

    /**
     * @param \Test\Model\Order[] $orders
     */
    public function __construct(?array $orders = null)
    {
        $this->Orders = $orders;
    }

    /**
     * @return \Test\Model\Order[]|null
     */
    public function getOrders(bool $throwExceptionIfNotInitialized = false): ?array
    {
        return $this->getPropertyValue('Orders', $throwExceptionIfNotInitialized);
    }

    public function setOrders(\Test\Model\Order ...$orders): self
    {
        $this->Orders = $orders;

        return $this;
    }
}
