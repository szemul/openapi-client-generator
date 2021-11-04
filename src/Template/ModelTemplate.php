<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

class ModelTemplate extends TemplateAbstract
{
    private string $className;

    /** @var ModelPropertyTemplate[] */
    private array $properties;

    public function __construct(string $rootNamespace, string $className, ModelPropertyTemplate ...$properties)
    {
        parent::__construct($rootNamespace);

        $this->className  = $className;
        $this->properties = $properties;
    }

    public function __toString(): string
    {
        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getModelNamespace()};
            
            class {$this->className} 
            {
                {$this->getProperties()}
                {$this->getGetters()}
                {$this->getSetters()}
            }
            MODEL;
    }

    private function getProperties(): string
    {
        $result = '';
        foreach ($this->properties as $property) {
            $result .= (string)$property . PHP_EOL;
        }

        return $result;
    }

    private function getGetters(): string
    {
        $result = '';
        foreach ($this->properties as $property) {
            $result .= $property->getGetter() . PHP_EOL;
        }

        return $result;
    }

    private function getSetters(): string
    {
        $result = '';
        foreach ($this->properties as $property) {
            $result .= $property->getSetter() . PHP_EOL;
        }

        return $result;
    }
}
