<?php
declare(strict_types=1);

namespace Test\Model;

class OrderCreate201Response extends ModelAbstract implements ResponseInterface
{
    use ResponseTrait;
    /**
     * @var int Id of the created order
     */
    protected int $id;

    public function __construct()
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
