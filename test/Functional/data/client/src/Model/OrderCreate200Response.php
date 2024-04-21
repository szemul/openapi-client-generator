<?php
declare(strict_types=1);

namespace Test\Model;

class OrderCreate200Response extends ModelAbstract
{
    /**
     * @var int Id of the task
     */
    protected int $taskId;

    public function __construct()
    {

    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }

    public function setTaskId(int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }
}
