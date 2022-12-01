<?php

declare(strict_types=1);

namespace Test\Model;

use Test\JsonSerializableTrait;
use Test\Exception\PropertyNotInitializedException;
use JsonSerializable;
use ReflectionException;
use ReflectionProperty;

abstract class ModelAbstract implements JsonSerializable
{
    use JsonSerializableTrait;

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
            return isset($this->{$propertyName})
                ? $this->{$propertyName}
                : null;
        }
    }
}
