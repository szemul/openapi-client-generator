<?php
declare(strict_types=1);

namespace Test\Model;

class Error400 extends ModelAbstract
{
    /**
     * @var string The code of the error
     */
    protected string $errorCode;
    /**
     * @var string Description of the error
     */
    protected string $errorMessage;
    /**
     * @var array List of the invalid params where the property is the parameter name and the value is the describing the issue
     */
    protected array $params;

    /**
     * @param array $params
     */
    public function __construct(string $errorCode, string $errorMessage, array $params)
    {
        $this->errorCode    = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->params       = $params;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function setErrorCode(string $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }
}
