<?php
declare(strict_types=1);

namespace Test\Model;

class UpdateOrderRequest extends ModelAbstract
{
    /**
     * @var string Human readable id of the order
     */
    protected string $friendlyId;
    /**
     * @var string Currency of the payment and prices
     */
    protected string $currencyCode;

    public function __construct(string $currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    public function getFriendlyId(): string
    {
        return $this->friendlyId;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setFriendlyId(string $friendlyId): self
    {
        $this->friendlyId = $friendlyId;

        return $this;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }
}
