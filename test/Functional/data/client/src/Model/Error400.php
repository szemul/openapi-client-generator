<?php
declare(strict_types=1);

namespace Test\Model;

class Error400 extends ModelAbstract
{
    /**
     * @var string|null The code of the error
     */
    protected ?string $errorCode;
    /**
     * @var string|null Description of the error
     */
    protected ?string $errorMessage;
    /**
     * @var array List of the invalid params where the property is the parameter name and the value is the describing the issue
     */
    protected ?array $params;

    /**
     * @param array $params
     */
    public function __construct(?string $errorCode = null, ?string $errorMessage = null, ?array $params = null)
    {
        $this->errorCode    = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->params       = $params;
    }

    public function getErrorCode(bool $throwExceptionIfNotInitialized = false): ?string
    {
        return $this->getPropertyValue('errorCode', $throwExceptionIfNotInitialized);
    }

    public function getErrorMessage(bool $throwExceptionIfNotInitialized = false): ?string
    {
        return $this->getPropertyValue('errorMessage', $throwExceptionIfNotInitialized);
    }

    /**
     * @return array|null
     */
    public function getParams(bool $throwExceptionIfNotInitialized = false): ?array
    {
        return $this->getPropertyValue('params', $throwExceptionIfNotInitialized);
    }

    public function setErrorCode(?string $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function setErrorMessage(?string $errorMessage): self
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
