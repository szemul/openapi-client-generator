<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseListInterfaceTemplate extends ClassTemplateAbstract
{
    public function __construct(private readonly LocationHelper $locationHelper)
    {
    }
    
    public function getDirectory(): string
    {
        return $this->locationHelper->getModelPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getModelNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'ResponseListInterface';
    }

    public function __toString(): string
    {
        return <<<ENUM
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->locationHelper->getModelNamespace()};
            
            interface ResponseListInterface
            {
                public function getItemClass(): string;
            }
            ENUM;
    }
}
