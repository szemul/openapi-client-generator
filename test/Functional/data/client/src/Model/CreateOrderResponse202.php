<?php
declare(strict_types=1);

namespace Test\Model;

class CreateOrderResponse202 extends ModelAbstract implements ResponseInterface
{
    use ResponseTrait;
    /**
     * @var string Message
     */
    protected string $id;

    public function __construct()
    {

    }

    public function getId(bool $throwExceptionIfNotInitialized = false): ?string
    {
        return $this->getPropertyValue('id', $throwExceptionIfNotInitialized);
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
