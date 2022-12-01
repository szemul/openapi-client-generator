<?php
declare(strict_types=1);

namespace Test\Model;

use Carbon\CarbonInterface;

class OrderCreateRequest extends ModelAbstract
{
    /**
     * @var string|null Human readable id of the order
     */
    protected ?string $friendlyId;
    /**
     * @var string Currency of the payment and prices
     */
    protected string $currencyCode;
    /**
     * @var \Test\Model\OrderProductRequest[] Ordered products
     */
    protected array $products;
    /**
     * @var \Carbon\CarbonInterface|null
     */
    protected ?CarbonInterface $createdAt;
    /**
     * @var string Id of the order in the source system
     */
    protected string $orderId;
    /**
     * @var \Test\Model\OrderSourceSystemRequest
     */
    protected OrderSourceSystemRequest $sourceSystem;

    /**
     * @param \Test\Model\OrderProductRequest[] $products
     */
    public function __construct(string $currencyCode, array $products, string $orderId, OrderSourceSystemRequest $sourceSystem, ?string $friendlyId = null, ?CarbonInterface $createdAt = null)
    {
        $this->friendlyId   = $friendlyId;
        $this->currencyCode = $currencyCode;
        $this->products     = $products;
        $this->createdAt    = $createdAt;
        $this->orderId      = $orderId;
        $this->sourceSystem = $sourceSystem;
    }

    public function getFriendlyId(bool $throwExceptionIfNotInitialized = false): ?string
    {
        return $this->getPropertyValue('friendlyId', $throwExceptionIfNotInitialized);
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return \Test\Model\OrderProductRequest[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCreatedAt(bool $throwExceptionIfNotInitialized = false): ?CarbonInterface
    {
        return $this->getPropertyValue('createdAt', $throwExceptionIfNotInitialized);
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getSourceSystem(): OrderSourceSystemRequest
    {
        return $this->sourceSystem;
    }

    public function setFriendlyId(?string $friendlyId): self
    {
        $this->friendlyId = $friendlyId;

        return $this;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function setProducts(\Test\Model\OrderProductRequest ...$products): self
    {
        $this->products = $products;

        return $this;
    }

    public function setCreatedAt(?CarbonInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setSourceSystem(OrderSourceSystemRequest $sourceSystem): self
    {
        $this->sourceSystem = $sourceSystem;

        return $this;
    }
}
