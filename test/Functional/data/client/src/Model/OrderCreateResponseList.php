<?php

declare(strict_types=1);

namespace Test\Model;

use JsonSerializable;

class OrderCreateResponseList implements ResponseListInterface, JsonSerializable
{
    /** @var OrderCreateResponse[] */
    private array $items = [];

    public function getItemClass(): string
    {
        return OrderCreateResponse::class;
    }

    public function add(OrderCreateResponse $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @return OrderCreateResponse[]
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
