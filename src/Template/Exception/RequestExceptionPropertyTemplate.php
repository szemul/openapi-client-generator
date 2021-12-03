<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class RequestExceptionPropertyTemplate extends TemplateAbstract
{
    private string       $name;
    private PropertyType $type;
    private ?string      $description = null;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $name,
        PropertyType $type,
        ?string $description = null
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->name        = $name;
        $this->type        = $type;
        $this->description = $description;
    }

    public function getGetter(): string
    {
        $documentation = '';
        $getterName    = 'get' . ucfirst($this->name);
        $returnType    = (string)$this->type === PropertyType::OBJECT
            ? '\\' . $this->type->getObjectClassname()
            : (string)$this->type;

        if ((string)$this->type === PropertyType::ARRAY) {
            $docType       = $this->getArrayItemType() . '[]';
            $documentation = <<<DOCUMENTATION
                /**
                 * @return {$docType}|null   {$this->description}
                 */
                
                DOCUMENTATION;
        } else {
            $documentation = empty($this->description)
                ? ''
                : <<<DOCUMENTATION
                    /**
                     * @return {$returnType}|null   {$this->description}
                     */
                    
                    DOCUMENTATION;
        }

        $getter = <<<GETTER
            public function {$getterName}(): ?{$returnType}
            {
                return \$this->getResponseDecoded()['{$this->name}'] ?? null;
            }
            GETTER;

        return $documentation . $getter;
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
