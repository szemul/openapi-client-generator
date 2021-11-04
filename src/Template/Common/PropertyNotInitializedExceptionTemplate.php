<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class PropertyNotInitializedExceptionTemplate extends TemplateAbstract
{
    public function __toString(): string
    {
        return <<<MODEL
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getModelNamespace()};
            
            use Exception;
            
            class PropertyNotInitializedException extends Exception
            {
            }
            MODEL;

    }
}
