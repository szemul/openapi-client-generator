<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class JsonSerializableTraitTemplate extends ClassTemplateAbstract
{
    public function __toString(): string
    {
        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Carbon\CarbonInterface;
            
            trait {$this->getClassName()}
            {
                public function jsonSerialize()
                {
                    \$properties = get_object_vars(\$this);
            
                    foreach (\$properties as \$index => \$property) {
                        if (\$property instanceof CarbonInterface) {
                            \$properties[\$index] = \$property->toIso8601ZuluString();
                        }
                    }
            
                    return \$properties;
                }
            
                public function toArray(): array
                {
                    return json_decode(json_encode(\$this), true);
                }
            }
            MODEL;
    }

    public function getDirectory():string
    {
        return $this->getLocationHelper()->getRootPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getRootNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'JsonSerializableTrait';
    }
}
