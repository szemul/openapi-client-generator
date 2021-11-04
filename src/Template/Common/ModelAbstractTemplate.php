<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;


use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ModelAbstractTemplate extends TemplateAbstract
{
    public function __toString(): string
    {
        return <<<MODEL
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getModelNamespace()};
            
            use {$this->getRootNamespace()}\TJsonSerializable;
            use {$this->getModelNamespace()}\PropertyNotInitializedException;
            use JsonSerializable;
            use ReflectionException;
            use ReflectionProperty;
            
            abstract class ModelAbstract implements JsonSerializable
            {
                use TJsonSerializable;
            
                /**
                 * @throws PropertyNotInitializedException
                 * @throws ReflectionException
                 */
                protected function getPropertyValue(string \$propertyName, bool \$throwExceptionIfNotInitialized)
                {
                    if (\$throwExceptionIfNotInitialized) {
                        \$propertyReflection = new ReflectionProperty(\$this, \$propertyName);
            
                        if (!\$propertyReflection->isInitialized(\$this)) {
                            throw new PropertyNotInitializedException();
                        }
            
                        return \$this->{\$propertyName};
                    } else {
                        return isset(\$this->key)
                            ? \$this->{\$propertyName}
                            : null;
                    }
                }
            }
            
            MODEL;

    }
}
