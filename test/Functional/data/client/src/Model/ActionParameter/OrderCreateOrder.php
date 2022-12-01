<?php
declare(strict_types=1);

namespace Test\Model\ActionParameter;

use Test\Model\OrderCreateRequest;

class OrderCreateOrder
{
    private OrderCreateRequest $requestModel;

    public function __construct(OrderCreateRequest $requestModel)
    {
        $this->requestModel = $requestModel;
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

    public function getRequestModel(): OrderCreateRequest
    {
        return $this->requestModel;
    }

    public function setRequestModel(OrderCreateRequest $model): self
    {
        $this->requestModel = $model;

        return $this;
    }

    public function hasRequestModel(): bool
    {
        return true;
    }
}
