<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class PropertyNotInitializedExceptionTemplate extends ClassTemplateAbstract
{
    public function __construct(private readonly LocationHelper $locationHelper)
    {
    }

    public function __toString(): string
    {
        return <<<MODEL
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Exception;
            
            class {$this->getClassName()} extends Exception
            {
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
        return 'PropertyNotInitializedException';
    }
}
