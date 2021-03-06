<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ModelTemplate extends ClassTemplateAbstract
{
    private TypeMapper $typeMapper;
    private string     $className;

    /** @var ModelPropertyTemplate[] */
    private array $properties;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        TypeMapper $typeMapper,
        string $modelName,
        ModelPropertyTemplate ...$properties
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->typeMapper = $typeMapper;
        $this->className  = $this->getStringHelper()->convertToClassName($modelName);
        $this->properties = $properties;
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
        return $this->className;
    }

    public function __toString(): string
    {
        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            {$this->getImports()}
            
            class {$this->getClassName()} extends ModelAbstract
            {
                {$this->getProperties()}
                {$this->getConstructor()}
                {$this->getGetters()}
                {$this->getSetters()}
            }
            MODEL;
    }

    private function getImports(): string
    {
        $classes = [];
        $result  = '';

        foreach ($this->properties as $property) {
            if ((string)$property->getType() === PropertyType::OBJECT) {
                $classes[] = $property->getType()->getObjectClassname();
            }
        }

        foreach (array_unique($classes) as $import) {
            $result .= 'USE ' . $import . ';' . PHP_EOL;
        }

        return $result;
    }

    private function getProperties(): string
    {
        $result = '';
        foreach ($this->properties as $property) {
            $result .= $property . PHP_EOL;
        }

        return $result;
    }

    private function getConstructor(): string
    {
        $requiredParams      = [];
        $optionalParams      = [];
        $paramSetters        = [];
        $paramDocumentations = [];

        foreach ($this->properties as $property) {
            if ((string)$property->getType() === PropertyType::ARRAY) {
                $paramDocumentations[] = $this->typeMapper->mapModelPropertyTemplateToDoc($property) . ' $' . $property->getName();
            }

            $param          = $this->typeMapper->mapModelPropertyTemplateToPhp($property) . ' $' . $property->getName();
            $paramSetters[] = '$this->' . $property->getName() . ' = $' . $property->getName() . ';';

            if ($property->isRequired()) {
                $requiredParams[] = $param;
            } else {
                $optionalParams[] = $param . ' = null';
            }
        }

        $params             = array_merge($requiredParams, $optionalParams);
        $paramList          = implode(', ', $params);
        $paramSettersString = implode(PHP_EOL, $paramSetters);
        $documentation      = '';

        if (!empty($paramDocumentations)) {
            $documentation = '/**' . PHP_EOL;
            foreach ($paramDocumentations as $paramDocumentation) {
                $documentation .= ' * @param ' . $paramDocumentation . PHP_EOL;
            }

            $documentation .= ' */' . PHP_EOL;
        }

        $constructor = <<<CONSTRUCTOR
            public function __construct({$paramList})
            {
                {$paramSettersString}
            }

            CONSTRUCTOR;

        return $documentation . $constructor;
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
