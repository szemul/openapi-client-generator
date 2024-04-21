<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;

class ModelPropertyTemplate
{
    public function __construct(
        private readonly TypeMapper $typeMapper,
        private readonly StringHelper $stringHelper,
        public readonly string $name,
        public readonly PropertyType $type,
        public readonly bool $isRequired,
        public readonly bool $isNullable,
        public readonly ?string $description = null
    ) {
    }

    public function __toString(): string
    {
        $propertyType = $this->typeMapper->mapModelPropertyTemplateToPhp($this);
        $docType      = $this->typeMapper->mapModelPropertyTemplateToDoc($this);
        $name         = $this->stringHelper->convertToPhpName($this->name);

        $varDoc = $docType;
        if (!empty($this->description)) {
            $varDoc .= ' ' . $this->description;
        }

        return <<<PROPERTY
            /**
             * @var {$varDoc}
             */
            protected {$propertyType} \${$name};
            PROPERTY;
    }

    public function getGetter(): string
    {
        $documentation = '';
        $propertyName  = $this->stringHelper->convertToPhpName($this->name);
        $getterName    = 'get' . ucfirst($propertyName);
        $returnType    = $this->isNullable ? '?' : '';
        $returnType .= (string)$this->type === PropertyType::OBJECT
            ? $this->type->getObjectClassname(false)
            : (string)$this->type;

        if ((string)$this->type === PropertyType::ARRAY) {
            $arrayItemType = $this->typeMapper->getArrayItemType($this->type);
            $docType       = empty($arrayItemType) ? 'array' : $arrayItemType . '[]';
            $docType .= $this->isNullable ? '|null' : '';
            $documentation = <<<DOCUMENTATION
                /**
                 * @return {$docType}
                 */
                
                DOCUMENTATION;
        }

        if ($this->isNullable) {
            $getter = <<<GETTER
                public function {$getterName}(bool \$throwExceptionIfNotInitialized = false): {$returnType}
                {
                    return \$this->getPropertyValue('{$propertyName}', \$throwExceptionIfNotInitialized);
                }
                GETTER;
        } else {
            $getter = <<<GETTER
                public function {$getterName}(): {$returnType}
                {
                    return \$this->{$propertyName};
                }
                GETTER;
        }

        return $documentation . $getter;
    }

    public function getSetter(): string
    {
        $propertyName     = $this->stringHelper->convertToPhpName($this->name);
        $setterName       = 'set' . ucfirst($propertyName);
        $ellipsisOperator = '';
        $parameterName    = '$' . $this->stringHelper->convertToMethodOrVariableName($this->name);

        if ($this->type->isScalar()) {
            $type = (string)$this->type;

            if ($this->isNullable) {
                $type = '?' . $type;
            }
        } elseif ((string)$this->type === PropertyType::OBJECT) {
            $type = $this->type->getObjectClassname(false);

            if ($this->isNullable) {
                $type = '?' . $type;
            }
        } elseif ((string)$this->type === PropertyType::ARRAY) {
            $type = $this->typeMapper->getArrayItemType($this->type);

            if (empty($type)) {
                $type = 'array';
            } else {
                $ellipsisOperator = '...';
            }
        }

        return <<<SETTER
            public function {$setterName}({$type} {$ellipsisOperator}{$parameterName}): self
            {
                \$this->{$propertyName} = $parameterName;
            
                return \$this;
            }
            SETTER;
    }
}
