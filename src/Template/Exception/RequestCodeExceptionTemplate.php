<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class RequestCodeExceptionTemplate extends ClassTemplateAbstract
{
    private int $errorCode;

    /** @var RequestExceptionPropertyTemplate[] */
    private array $properties = [];

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        int $errorCode,
        RequestExceptionPropertyTemplate ...$properties
    ) {
        parent::__construct($locationHelper, $stringHelper);

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
                public function __construct(string \$responseBody, array \$responseHeaders)
                {
                    parent::__construct({$this->errorCode}, \$responseBody, \$responseHeaders);
                }
            
            {$getters}
            }
            MODEL;
    }

    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getExceptionPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getExceptionNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'Request' . $this->errorCode . 'Exception';
    }
}
