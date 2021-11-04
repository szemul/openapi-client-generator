<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use Emul\OpenApiClientGenerator\PropertyType\Type;
use InvalidArgumentException;

class ModelPropertyTemplate extends TemplateAbstract
{
    private string  $name;
    private Type    $type;
    private bool    $isRequired;
    private ?string $description = null;

    public function __construct(string $rootNamespace, string $name, Type $type, bool $isRequired, ?string $description = null)
    {
        parent::__construct($rootNamespace);

        $this->name        = $name;
        $this->type        = $type;
        $this->isRequired  = $isRequired;
        $this->description = $description;
    }

    public function __toString(): string
    {
        if ($this->type->isScalar()) {
            $docType      = $this->type . ($this->isRequired ? '' : '|null');
            $propertyType = ($this->isRequired ? '' : '?') . $this->type;
        } elseif ((string)$this->type === Type::OBJECT) {
            $docType      = $this->type->getObjectClassname() . ($this->isRequired ? '' : '|null');
            $propertyType = ($this->isRequired ? '' : '?') . '\\' . $this->type->getObjectClassname();
        } elseif ((string)$this->type === Type::ARRAY) {
            $docType      = $this->getArrayItemType() . '[]';
            $propertyType = ($this->isRequired ? '' : '?') . $this->type;
        } else {
            throw new InvalidArgumentException('Unhandled property type: ' . $this->type);
        }

        $varDoc = $docType;
        if (!empty($this->description)) {
            $varDoc .= ' ' . $this->description;
        }

        return <<<DOC
            /**
             * @var {$varDoc}
             */
            protected {$propertyType} \${$this->name};
            DOC;
    }

    public function getGetter(): string
    {
        $documentation = '';
        $getterName    = 'get' . ucfirst($this->name);
        $returnType    = $this->isRequired ? '' : '?';
        $returnType    .= (string)$this->type === Type::OBJECT
            ? '\\' . $this->type->getObjectClassname()
            : (string)$this->type;

        if ((string)$this->type === Type::ARRAY) {
            $docType       = $this->getArrayItemType() . '[]';
            $docType       .= $this->isRequired ? '' : '|null';
            $documentation = <<<DOCUMENTATION
                /**
                 * @return {$docType}
                 */
                
                DOCUMENTATION;
        }

        if ($this->isRequired) {
            $getter = <<<GETTER
                public function {$getterName}(): {$returnType}
                {
                    return \$this->{$this->name};
                }
                GETTER;
        } else {
            $getter = <<<GETTER
                public function {$getterName}(bool \$throwExceptionIfNotInitialized = false): {$returnType}
                {
                    return \$this->getPropertyValue('{$this->name}', \$throwExceptionIfNotInitialized);
                }
                GETTER;
        }

        return $documentation . $getter;
    }

    public function getSetter(): string
    {
        $setterName       = 'set' . ucfirst($this->name);
        $ellipsisOperator = '';
        $variableName     = '$' . $this->name;

        if ($this->type->isScalar()) {
            $type = (string)$this->type;

            if (!$this->isRequired) {
                $type = '?' . $type;
            }
        } elseif ((string)$this->type === Type::OBJECT) {
            $type = '\\' . $this->type->getObjectClassname();

            if (!$this->isRequired) {
                $type = '?' . $type;
            }
        } elseif ((string)$this->type === Type::ARRAY) {
            $ellipsisOperator = '...';
            $type             = $this->getArrayItemType(true);
        }

        return <<<SETTER
            public function {$setterName}({$type} {$ellipsisOperator}{$variableName}): self
            {
                \$this->{$this->name} = $variableName;
            
                return \$this;
            }
            SETTER;
    }

    private function getArrayItemType(bool $addRootNameSpace = false): string
    {
        if ($this->type->getArrayItemType()->isScalar()) {
            return (string)$this->type->getArrayItemType();
        } else {
            return ($addRootNameSpace ? '\\' : '') . $this->type->getArrayItemType()->getObjectClassname();
        }
    }
}
