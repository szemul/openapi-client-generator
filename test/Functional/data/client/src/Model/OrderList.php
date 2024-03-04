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
     * @param \Test\Model\Order[] $Orders
     */
    public function __construct(?array $Orders = null)
    {
        $this->Orders = $Orders;
    }

    /**
     * @return \Test\Model\Order[]|null
     */
    public function getOrders(bool $throwExceptionIfNotInitialized = false): ?array
    {
        return $this->getPropertyValue('Orders', $throwExceptionIfNotInitialized);
    }

    public function setOrders(\Test\Model\Order ...$Orders): self
    {
        $this->Orders = $Orders;

        return $this;
    }
}
