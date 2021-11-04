<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Example;

use Emul\OpenApiClientGenerator\TJsonSerializable;
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
    protected function getPropertyValue(string $propertyName, bool $throwExceptionIfNotInitialized)
    {
        if ($throwExceptionIfNotInitialized) {
            $propertyReflection = new ReflectionProperty($this, $propertyName);

            if (!$propertyReflection->isInitialized($this)) {
                throw new PropertyNotInitializedException();
            }

            return $this->{$propertyName};
        } else {
            return isset($this->key)
                ? $this->{$propertyName}
                : null;
        }
    }
}
