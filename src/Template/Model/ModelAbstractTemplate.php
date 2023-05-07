<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ModelAbstractTemplate extends ClassTemplateAbstract
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
            
            use {$this->locationHelper->getRootNamespace()}\JsonSerializableTrait;
            use {$this->locationHelper->getExceptionNamespace()}\PropertyNotInitializedException;
            use JsonSerializable;
            use ReflectionException;
            use ReflectionProperty;
            
            abstract class {$this->getClassName()} implements JsonSerializable
            {
                use JsonSerializableTrait;
            
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
                        return isset(\$this->{\$propertyName})
                            ? \$this->{\$propertyName}
                            : null;
                    }
                }
            }
            
            MODEL;
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
        return 'ModelAbstract';
    }
}
