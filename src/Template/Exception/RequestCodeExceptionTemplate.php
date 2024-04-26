<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class RequestCodeExceptionTemplate extends ClassTemplateAbstract
{
    private int $errorCode;

    /** @var RequestExceptionPropertyTemplate[] */
    private array $properties = [];

    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly ClassHelper $classHelper,
        int $errorCode,
        RequestExceptionPropertyTemplate ...$properties
    ) {
        $this->errorCode  = $errorCode;
        $this->properties = $properties;
    }

    public function __toString(): string
    {
        $getters = '';
        foreach ($this->properties as $property) {
            $getters .= $property->getGetter() . PHP_EOL;
        }

        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            class {$this->getClassName()} extends RequestException
            {
                public function __construct(string \$responseBody, array \$responseHeaders, string \$requestUrl, string \$requestMethod, string \$requestBody, array \$requestHeaders)
                {
                    parent::__construct({$this->errorCode}, \$responseBody, \$responseHeaders, \$requestUrl, \$requestMethod, \$requestBody, \$requestHeaders);
                }
            
            {$getters}
            }
            MODEL;
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getExceptionPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getExceptionNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->classHelper->getRequestExceptionClassName($this->errorCode);
    }
}
