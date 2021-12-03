<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ModelAbstractTemplate extends ClassTemplateAbstract
{
    public function __toString(): string
    {
        return <<<MODEL
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use {$this->getLocationHelper()->getRootNamespace()}\TJsonSerializable;
            use {$this->getLocationHelper()->getExceptionNamespace()}\PropertyNotInitializedException;
            use JsonSerializable;
            use ReflectionException;
            use ReflectionProperty;
            
            abstract class {$this->getClassName()} implements JsonSerializable
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

    public function getDirectory():string
    {
        return $this->getLocationHelper()->getModelPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getModelNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'ModelAbstract';
    }
}
