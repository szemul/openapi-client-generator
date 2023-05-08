<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;

class ModelPropertyTemplate
{
    public function __construct(
        private readonly TypeMapper $typeMapper,
        private readonly string $name,
        private readonly PropertyType $type,
        private readonly bool $isRequired,
        private readonly ?string $description = null
    ) {
    }

    public function __toString(): string
    {
        $propertyType = $this->typeMapper->mapModelPropertyTemplateToPhp($this);
        $docType      = $this->typeMapper->mapModelPropertyTemplateToDoc($this);

        $varDoc = $docType;
        if (!empty($this->description)) {
            $varDoc .= ' ' . $this->description;
        }

        return <<<PROPERTY
            /**
             * @var {$varDoc}
             */
            protected {$propertyType} \${$this->name};
            PROPERTY;
    }

    public function getGetter(): string
    {
        $documentation = '';
        $getterName    = 'get' . ucfirst($this->name);
        $returnType    = $this->isRequired ? '' : '?';
        $returnType .= (string)$this->type === PropertyType::OBJECT
            ? $this->type->getObjectClassname(false)
            : (string)$this->type;

        if ((string)$this->type === PropertyType::ARRAY) {
            $arrayItemType = $this->typeMapper->getArrayItemType($this->type);
            $docType       = empty($arrayItemType) ? 'array' : $arrayItemType . '[]';
            $docType .= $this->isRequired ? '' : '|null';
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
        } elseif ((string)$this->type === PropertyType::OBJECT) {
            $type = $this->type->getObjectClassname(false);

            if (!$this->isRequired) {
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
            public function {$setterName}({$type} {$ellipsisOperator}{$variableName}): self
            {
                \$this->{$this->name} = $variableName;
            
                return \$this;
            }
            SETTER;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
