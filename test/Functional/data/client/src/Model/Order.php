<?php
declare(strict_types=1);

namespace Test\Model;

class Order extends ModelAbstract
{
    /**
     * @var int|null Id of the created order
     */
    protected ?int $orderId;
    /**
     * @var string|null Creation time of order
     */
    protected ?string $createdAt;

    public function __construct(?int $orderId = null, ?string $createdAt = null)
    {
        $this->orderId   = $orderId;
        $this->createdAt = $createdAt;
    }

    public function getOrderId(bool $throwExceptionIfNotInitialized = false): ?int
    {
        return $this->getPropertyValue('orderId', $throwExceptionIfNotInitialized);
    }

    public function getCreatedAt(bool $throwExceptionIfNotInitialized = false): ?string
    {
        return $this->getPropertyValue('createdAt', $throwExceptionIfNotInitialized);
    }

    public function setOrderId(?int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
