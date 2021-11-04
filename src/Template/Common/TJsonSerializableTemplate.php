<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class TJsonSerializableTemplate extends TemplateAbstract
{
    public function __toString(): string
    {
        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getRootNamespace()};
            
            use Carbon\CarbonInterface;
            
            trait TJsonSerializable
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
}
