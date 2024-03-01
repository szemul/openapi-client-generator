<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterType;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
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
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper,
        string $className,
        ?string $requestModelClassName,
        Parameter ...$parameters
    ) {
        $this->className             = $className;
        $this->requestModelClassName = $requestModelClassName;
        $this->parameters            = $parameters;
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getActionParameterPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getActionParameterNamespace();
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

        return 'use ' . $this->locationHelper->getModelNamespace() . '\\' . $this->requestModelClassName . ';' . PHP_EOL;
    }

    private function getProperties(): string
    {
        $result = '';

        if (!empty($this->requestModelClassName)) {
            $result .= 'private ' . $this->requestModelClassName . ' $' . self::REQUEST_MODEL_PROPERTY_NAME . ';' . PHP_EOL;
        }

        foreach ($this->parameters as $parameter) {
            $propertyName = $this->getPropertyName($parameter);
            $type         = $this->getType($parameter);

            if ((string)$parameter->getValueType() === PropertyType::ARRAY) {
                $itemType = $parameter->getValueType()->getArrayItemType();
                $docType  = empty($itemType) ? 'array' : $itemType . '[]';
                $result .= "/** @var $docType */" . PHP_EOL;
            }

            $result .= 'private ' . $type . ' $' . $propertyName . ';' . PHP_EOL;
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
            $variableName = $this->stringHelper->convertToMethodOrVariableName($parameter->getName());
            $type         = $this->getType($parameter);
            $param        = $type . ' $' . $variableName;
            $setters[]    = '$this->' . $this->getPropertyName($parameter) . ' = $' . $variableName . ';';

            if ($parameter->isRequired()) {
                $requiredParams[] = $param;
            } else {
                $optionalParams[] = $param . ' = null';
            }
        }

        $constructorParamList = implode(', ', array_merge($requiredParams, $optionalParams));
        $constructorBody      = implode(PHP_EOL, $setters);
        $phpDoc               = $this->getParameterDoc(...$this->parameters);

        return <<<CONSTRUCTOR
            {$phpDoc}
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
            $getterName   = $this->stringHelper->convertToMethodOrVariableName('get_' . $propertyName);
            $result       = <<<GETTER
                public function $getterName(): $this->requestModelClassName
                {
                    return \$this->$propertyName;
                }

                GETTER;
        }

        foreach ($this->parameters as $parameter) {
            if ((string)$parameter->getValueType() === PropertyType::OBJECT) {
                $returnType      = ($parameter->isRequired() ? '' : '?') . 'string';
                $returnStatement = "return is_object(\$this->{$this->getPropertyName($parameter)}) ? (string)\$this->{$this->getPropertyName($parameter)} : null;";
            } elseif ((string)$parameter->getValueType() === PropertyType::ARRAY) {
                $returnType = $parameter->getPhpValueType();
                $itemType   = $parameter->getValueType()->getArrayItemType()->isScalar()
                    ? $parameter->getValueType()->getArrayItemType()
                    : '\\' . $parameter->getValueType()->getArrayItemType()->getObjectClassname();

                $returnStatement = <<<RETURN
                    return array_map(
                        fn($itemType \$item) => (string)\$item,
                        \$this->{$this->getPropertyName($parameter)} ?? []
                    );

                    RETURN;
            } else {
                $returnType      = $parameter->getPhpValueType();
                $returnStatement = "return \$this->{$this->getPropertyName($parameter)};";
            }

            $result .= <<<GETTER
                public function {$this->getPropertyGetterName($parameter)}(): {$returnType}
                {
                    {$returnStatement}
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
            $setterName   = $this->stringHelper->convertToMethodOrVariableName('set_' . $propertyName);
            $result       = <<<SETTER
                public function $setterName($this->requestModelClassName \$model): self
                {
                    \$this->$propertyName = \$model;
                    
                    return \$this;
                }
                
                SETTER;
        }

        foreach ($this->parameters as $parameter) {
            $type   = $this->getType($parameter);
            $phpDoc = $this->getParameterDocLine($parameter, 'parameter');

            $phpDoc = empty($phpDoc)
                ? ''
                : '/**' . PHP_EOL . $phpDoc . PHP_EOL . '*/';

            $result .= <<<SETTER
                {$phpDoc}
                public function {$this->getPropertySetterName($parameter)}({$type} \$parameter): self
                {
                    \$this->{$this->getPropertyName($parameter)} = \$parameter;

                    return \$this;
                }

                SETTER;
        }

        return $result;
    }

    private function getType(Parameter $parameter): string
    {
        if ((string)$parameter->getValueType() === PropertyType::OBJECT) {
            $type = empty($parameter->getValueType()->getObjectClassname())
                ? 'object'
                : '\\' . $parameter->getValueType()->getObjectClassname();

            if (!$parameter->isRequired()) {
                $type .= '|null';
            }
        } else {
            $type = $parameter->getPhpValueType();
        }

        return $type;
    }

    private function getParameterDoc(Parameter ...$parameters): string
    {
        $phpDocLines = [];
        foreach ($parameters as $parameter) {
            $parameterDocLine = $this->getParameterDocLine($parameter, $parameter->getName());
            if (!empty($parameterDocLine)) {
                $phpDocLines[] = $parameterDocLine;
            }
        }

        return empty($phpDocLines)
            ? ''
            : '/**' . PHP_EOL . implode(PHP_EOL, $phpDocLines) . PHP_EOL . '*/';
    }

    private function getParameterDocLine(Parameter $parameter, string $parameterName): string
    {
        if ((string)$parameter->getValueType() !== PropertyType::ARRAY) {
            return '';
        }
        $variableName = $this->stringHelper->convertToMethodOrVariableName($parameterName);
        $itemType     = $parameter->getValueType()->getArrayItemTypeString();
        $docType      = empty($itemType) ? 'array' : $itemType . '[]';

        return "* @var $docType $" . $variableName . PHP_EOL;
    }

    private function getPropertyName(Parameter $parameter): string
    {
        return $this->stringHelper->convertToMethodOrVariableName($parameter->getType() . '_' . $parameter->getName());
    }

    private function getPropertyGetterName(Parameter $parameter): string
    {
        return $this->stringHelper->convertToMethodOrVariableName('get_' . $this->getPropertyName($parameter));
    }

    private function getPropertySetterName(Parameter $parameter): string
    {
        return $this->stringHelper->convertToMethodOrVariableName('set_' . $this->getPropertyName($parameter));
    }
}
