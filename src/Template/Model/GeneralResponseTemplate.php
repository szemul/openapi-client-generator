<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class GeneralResponseTemplate extends ClassTemplateAbstract
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
        return 'GeneralResponse';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->locationHelper->getModelNamespace()};
            
            use JsonSerializable;
            
            class {$this->getClassName()} implements ResponseInterface
            {
                use ResponseTrait;
            }
            TEMPLATE;
    }
}
