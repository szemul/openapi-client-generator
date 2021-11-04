<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

class RequestCodeExceptionTemplate extends TemplateAbstract
{
    private int $errorCode;

    /** @var ErrorPropertyTemplate[] */
    private array $properties = [];

    public function __construct(string $rootNamespace, int $errorCode, ErrorPropertyTemplate ...$properties)
    {
        parent::__construct($rootNamespace);

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
            
            namespace {$this->getRootNamespace()};
            
            class Request{$this->errorCode}Exception extends RequestException
            {
                public function __construct(string \$responseBody, array \$responseHeaders)
                {
                    parent::__construct({$this->errorCode}, \$responseBody, \$responseHeaders);
                }
            
            {$getters}
            }
            MODEL;
    }
}
