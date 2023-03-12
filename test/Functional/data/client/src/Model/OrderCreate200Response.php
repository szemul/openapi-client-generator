<?php
declare(strict_types=1);

namespace Test\Model;

class OrderCreate200Response extends ModelAbstract
{
    /**
     * @var int|null Id of the task
     */
    protected ?int $taskId;

    public function __construct(?int $taskId = null)
    {
        $this->taskId = $taskId;
    }

    public function getTaskId(bool $throwExceptionIfNotInitialized = false): ?int
    {
        return $this->getPropertyValue('taskId', $throwExceptionIfNotInitialized);
    }

    public function setTaskId(?int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }
}
