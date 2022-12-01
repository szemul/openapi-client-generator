<?php
declare(strict_types=1);

namespace Test\Model;

use Carbon\CarbonInterface;

class OrderSourceSystemRequest extends ModelAbstract
{
    /**
     * @var int Id of the source account
     */
    protected int $accountId;
    /**
     * @var string Name of the food system the request comes from
     */
    protected string $name;
    /**
     * @var \Carbon\CarbonInterface
     */
    protected CarbonInterface $pushDeadline;
    /**
     * @var string Url of the caller service what will be called once the order has been pushed successfully
     */
    protected string $successCallbackUrl;
    /**
     * @var string Url of the caller service what will be called if we failed to push the order
     */
    protected string $failedCallbackUrl;

    public function __construct(int $accountId, string $name, CarbonInterface $pushDeadline, string $successCallbackUrl, string $failedCallbackUrl)
    {
        $this->accountId          = $accountId;
        $this->name               = $name;
        $this->pushDeadline       = $pushDeadline;
        $this->successCallbackUrl = $successCallbackUrl;
        $this->failedCallbackUrl  = $failedCallbackUrl;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPushDeadline(): CarbonInterface
    {
        return $this->pushDeadline;
    }

    public function getSuccessCallbackUrl(): string
    {
        return $this->successCallbackUrl;
    }

    public function getFailedCallbackUrl(): string
    {
        return $this->failedCallbackUrl;
    }

    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setPushDeadline(CarbonInterface $pushDeadline): self
    {
        $this->pushDeadline = $pushDeadline;

        return $this;
    }

    public function setSuccessCallbackUrl(string $successCallbackUrl): self
    {
        $this->successCallbackUrl = $successCallbackUrl;

        return $this;
    }

    public function setFailedCallbackUrl(string $failedCallbackUrl): self
    {
        $this->failedCallbackUrl = $failedCallbackUrl;

        return $this;
    }
}
