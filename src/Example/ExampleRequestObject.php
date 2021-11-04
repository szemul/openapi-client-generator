<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Example;

use Carbon\CarbonInterface;

class ExampleRequestObject extends ModelAbstract
{
    protected int                       $id;
    protected string                    $name;
    protected CarbonInterface           $createdAt;
    protected ExampleInnerRequestObject $innerObject;
    /** @var \Carbon\CarbonInterface[] */
    protected array            $dates = [];
    protected ?CarbonInterface $updatedAt;

    /**
     * @param CarbonInterface[] $dates
     */
    public function __construct(
        int $id,
        string $name,
        CarbonInterface $createdAt,
        ExampleInnerRequestObject $innerObject,
        array $dates
    ) {
        $this->id          = $id;
        $this->name        = $name;
        $this->createdAt   = $createdAt;
        $this->innerObject = $innerObject;
        $this->dates       = $dates;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): CarbonInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CarbonInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getInnerObject(): ExampleInnerRequestObject
    {
        return $this->innerObject;
    }

    public function setInnerObject(ExampleInnerRequestObject $innerObject): self
    {
        $this->innerObject = $innerObject;

        return $this;
    }

    /**
     * @return CarbonInterface[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    public function setDates(CarbonInterface ...$dates): self
    {
        $this->dates = $dates;

        return $this;
    }

    /**
     * @throws PropertyNotInitializedException
     */
    public function getUpdatedAt(bool $throwExceptionIfNotInitialized = false): ?CarbonInterface
    {
        return $this->getPropertyValue('updatedAt', $throwExceptionIfNotInitialized);
    }

    public function setUpdatedAt(?CarbonInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
