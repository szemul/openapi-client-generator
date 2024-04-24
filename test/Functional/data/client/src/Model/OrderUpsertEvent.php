<?php
declare(strict_types=1);

namespace Test\Model;

use Test\Model\Enum\OrderUpsertEventAction;

class OrderUpsertEvent extends ModelAbstract
{
    /**
     * @var string The version of the entity. The format is v1, v2, etc
     */
    protected string $version;
    /**
     * @var int ID of the account that owns this entity
     */
    protected int $accountId;
    /**
     * @var \Test\Model\Enum\OrderUpsertEventAction The action this entity describes
     */
    protected OrderUpsertEventAction $action;
    /**
     * @var array Details of the order. The type depends on the action
     */
    protected array $order;

    /**
     * @param array $order
     */
    public function __construct(string $version, int $accountId, OrderUpsertEventAction $action, array $order)
    {
        $this->version   = $version;
        $this->accountId = $accountId;
        $this->action    = $action;
        $this->order     = $order;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getAction(): OrderUpsertEventAction
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function setAction(OrderUpsertEventAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function setOrder(array $order): self
    {
        $this->order = $order;

        return $this;
    }
}
