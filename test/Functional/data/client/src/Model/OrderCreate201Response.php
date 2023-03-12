<?php
declare(strict_types=1);

namespace Test\Model;

class OrderCreate201Response extends ModelAbstract implements ResponseInterface
{
    use ResponseTrait;
    /**
     * @var int|null Id of the created order
     */
    protected ?int $id;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(bool $throwExceptionIfNotInitialized = false): ?int
    {
        return $this->getPropertyValue('id', $throwExceptionIfNotInitialized);
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
