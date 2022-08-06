<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ActionParameterTemplate extends ClassTemplateAbstract
{
    private const REQUEST_MODEL_PROPERTY_NAME = 'requestModel';

    private string  $className;
    private ?string $requestModelClassName = null;
    /** @var Parameter[] */
    private array $parameters;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $className,
        ?string $requestModelClassName,
        Parameter ...$parameters
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->className             = $className;
        $this->requestModelClassName = $requestModelClassName;
        $this->parameters            = $parameters;
    }

    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getActionParameterPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getActionParameterNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->className;
    }

    public function __toString(): string
    {
        $hasRequestModel = empty($this->requestModelClassName)
            ? 'false'
            : 'true';

        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            {$this->getImports()}
            
            class {$this->getClassName()}
            {
                {$this->getProperties()}
                {$this->getConstructor()}
                {$this->getGettersByType()}
                {$this->getGetters()}
                {$this->getSetters()}
                
                public function hasRequestModel(): bool
                {
                    return {$hasRequestModel};
                }
            }
            MODEL;
    }

    private function getImports(): string
    {
        if (empty($this->requestModelClassName)) {
            return '';
        }

        return 'use ' . $this->getLocationHelper()->getModelNamespace() . '\\' . $this->requestModelClassName . ';' . PHP_EOL;
    }

    private function getProperties(): string
    {
        $result = '';

        if (!empty($this->requestModelClassName)) {
            $result .= 'private ' . $this->requestModelClassName . ' $' . self::REQUEST_MODEL_PROPERTY_NAME . ';' . PHP_EOL;
        }

        foreach ($this->parameters as $parameter) {
            $propertyName = $this->getPropertyName($parameter);
            $type         = $parameter->getPhpValueType();
            $result       .= 'private ' . $type . ' $' . $propertyName . ';' . PHP_EOL;
        }

        return $result;
    }

    private function getConstructor(): string
    {
        $requiredParams = [];
        $optionalParams = [];
        $setters        = [];

        if (!empty($this->requestModelClassName)) {
            $requiredParams[] = $this->requestModelClassName . ' $' . self::REQUEST_MODEL_PROPERTY_NAME;
            $setters[]        = '$this->' . self::REQUEST_MODEL_PROPERTY_NAME . ' = $' . self::REQUEST_MODEL_PROPERTY_NAME . ';';
        }

        foreach ($this->parameters as $parameter) {
            $param     = $parameter->getPhpValueType() . ' $' . $parameter->getName();
            $setters[] = '$this->' . $this->getPropertyName($parameter) . ' = $' . $parameter->getName() . ';';

            if ($parameter->isRequired()) {
                $requiredParams[] = $param;
            } else {
                $optionalParams[] = $param . ' = null';
            }
        }

        $constructorParamList = implode(', ', array_merge($requiredParams, $optionalParams));
        $constructorBody      = implode(PHP_EOL, $setters);

        return <<<CONSTRUCTOR
            public function __construct({$constructorParamList})
            {
                {$constructorBody}
            }
            CONSTRUCTOR;
    }

    private function getGettersByType(): string
    {
        $pathParameters   = [];
        $queryParameters  = [];
        $headerParameters = [];

        foreach ($this->parameters as $parameter) {
            switch ((string)$parameter->getType()) {
                case ParameterType::PATH:
                    $pathParameters[] = $parameter;
                    break;
                case ParameterType::QUERY:
                    $queryParameters[] = $parameter;
                    break;
                case ParameterType::HEADER:
                    $headerParameters[] = $parameter;
                    break;
            }
        }

        return $this->getGetterByType('path', ...$pathParameters) . PHP_EOL
            . $this->getGetterByType('query', ...$queryParameters) . PHP_EOL
            . $this->getGetterByType('header', ...$headerParameters);
    }

    private function getGetterByType(string $type, Parameter ...$parameters): string
    {
        $getters = [];
        foreach ($parameters as $parameter) {
            $getterName = $this->getPropertyGetterName($parameter);
            $getters[]  = "'" . $parameter->getName() . "' => '" . $getterName . "'";
        }
        $type     = ucfirst($type);
        $nameList = implode(', ', $getters);

        return <<<GETTER
            public function get{$type}ParameterGetters(): array
            {
                return [{$nameList}];
            }
            GETTER;
    }

    private function getGetters(): string
    {
        $result = '';

        if (!empty($this->requestModelClassName)) {
            $propertyName = self::REQUEST_MODEL_PROPERTY_NAME;
            $getterName   = $this->getStringHelper()->convertToMethodOrVariableName('get_' . $propertyName);
            $result       = <<<GETTER
                public function $getterName(): $this->requestModelClassName
                {
                    return \$this->$propertyName;
                }

                GETTER;
        }

        foreach ($this->parameters as $parameter) {
            $result .= <<<GETTER
                public function {$this->getPropertyGetterName($parameter)}(): {$parameter->getPhpValueType()}
                {
                    return \$this->{$this->getPropertyName($parameter)};
                }

                GETTER;
        }

        return $result;
    }

    private function getSetters(): string
    {
        $result = '';

        if (!empty($this->requestModelClassName)) {
            $propertyName = self::REQUEST_MODEL_PROPERTY_NAME;
            $setterName   = $this->getStringHelper()->convertToMethodOrVariableName('set_' . $propertyName);
            $result       = <<<SETTER
                public function $setterName($this->requestModelClassName \$model): self
                {
                    \$this->$propertyName = \$model;
                    
                    return \$this;
                }
                
                SETTER;
        }

        foreach ($this->parameters as $parameter) {
            $result .= <<<SETTER
                public function {$this->getPropertySetterName($parameter)}({$parameter->getPhpValueType()} \$parameter): self
                {
                    \$this->{$this->getPropertyName($parameter)} = \$parameter;

                    return \$this;
                }

                SETTER;
        }

        return $result;
    }

    private function getPropertyName(Parameter $parameter): string
    {
        return $this->getStringHelper()->convertToMethodOrVariableName($parameter->getType() . '_' . $parameter->getName());
    }

    private function getPropertyGetterName(Parameter $parameter): string
    {
        return $this->getStringHelper()->convertToMethodOrVariableName('get_' . $this->getPropertyName($parameter));
    }

    private function getPropertySetterName(Parameter $parameter): string
    {
        return $this->getStringHelper()->convertToMethodOrVariableName('set_' . $this->getPropertyName($parameter));
    }
}
