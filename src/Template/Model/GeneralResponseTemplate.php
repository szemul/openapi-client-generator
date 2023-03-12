<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class GeneralResponseTemplate extends ClassTemplateAbstract
{
    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper
    ) {
        parent::__construct($locationHelper, $stringHelper);
    }

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
        return 'GeneralResponse';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getLocationHelper()->getModelNamespace()};
            
            use JsonSerializable;
            
            class {$this->getClassName()} implements ResponseInterface
            {
                use ResponseTrait;
            }
            TEMPLATE;
    }
}
