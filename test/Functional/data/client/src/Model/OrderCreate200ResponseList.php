<?php

declare(strict_types=1);

namespace Test\Model;

use JsonSerializable;

class OrderCreate200ResponseList implements ResponseListInterface, ResponseInterface, JsonSerializable
{
    use ResponseTrait;

    /** @var OrderCreate200Response[] */
    private array $items = [];

    public function getItemClass(): string
    {
        return OrderCreate200Response::class;
    }

    public function add(OrderCreate200Response $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @return OrderCreate200Response[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }
}
