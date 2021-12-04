<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseListInterfaceTemplate extends ClassTemplateAbstract
{
    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getModelPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getModelNamespace();
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
            
            namespace {$this->getLocationHelper()->getModelNamespace()};
            
            interface ResponseListInterface
            {
                public function getItemClass(): string;
            }
            ENUM;
    }
}
