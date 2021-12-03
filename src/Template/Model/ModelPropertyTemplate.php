<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ModelPropertyTemplate extends TemplateAbstract
{
    private TypeMapper   $typeMapper;
    private string       $name;
    private PropertyType $type;
    private bool         $isRequired;
    private ?string      $description = null;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        TypeMapper $typeMapper,
        string $name,
        PropertyType $type,
        bool $isRequired,
        ?string $description = null
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->typeMapper  = $typeMapper;
        $this->name        = $name;
        $this->type        = $type;
        $this->isRequired  = $isRequired;
        $this->description = $description;
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
        $returnType    .= (string)$this->type === PropertyType::OBJECT
            ? $this->type->getObjectClassname(false)
            : (string)$this->type;

        if ((string)$this->type === PropertyType::ARRAY) {
            $docType       = $this->typeMapper->getArrayItemType($this->type) . '[]';
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
        } elseif ((string)$this->type === PropertyType::OBJECT) {
            $type = $this->type->getObjectClassname(false);

            if (!$this->isRequired) {
                $type = '?' . $type;
            }
        } elseif ((string)$this->type === PropertyType::ARRAY) {
            $ellipsisOperator = '...';
            $type             = $this->typeMapper->getArrayItemType($this->type);
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
