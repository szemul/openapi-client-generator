<?php
declare(strict_types=1);

namespace Test\Model;

class OrderProductRequest extends ModelAbstract
{
    /**
     * @var string Name of the extra
     */
    protected string $name;
    /**
     * @var string Id of the extra product
     */
    protected string $productId;

    public function __construct(string $name, string $productId)
    {
        $this->name      = $name;
        $this->productId = $productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setProductId(string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }
}
