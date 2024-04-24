<?php
declare(strict_types=1);

namespace Test\Model;

class Order extends ModelAbstract
{
    /**
     * @var int Id of the created order
     */
    protected int $orderId;
    /**
     * @var string Creation time of order
     */
    protected string $createdAt;

    public function __construct(int $orderId, string $createdAt)
    {
        $this->orderId   = $orderId;
        $this->createdAt = $createdAt;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
