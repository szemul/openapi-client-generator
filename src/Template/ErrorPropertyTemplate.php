<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use Emul\OpenApiClientGenerator\PropertyType\Type;

class ErrorPropertyTemplate extends TemplateAbstract
{
    private string  $name;
    private Type    $type;
    private ?string $description = null;

    public function __construct(string $rootNamespace, string $name, Type $type, ?string $description = null)
    {
        parent::__construct($rootNamespace);

        $this->name        = $name;
        $this->type        = $type;
        $this->description = $description;
    }

    public function getGetter(): string
    {
        $documentation = '';
        $getterName    = 'get' . ucfirst($this->name);
        $returnType    = (string)$this->type === Type::OBJECT
            ? '\\' . $this->type->getObjectClassname()
            : (string)$this->type;

        if ((string)$this->type === Type::ARRAY) {
            $docType       = $this->getArrayItemType() . '[]';
            $documentation = <<<DOCUMENTATION
                /**
                 * @return {$docType}|null   {$this->description}
                 */
                
                DOCUMENTATION;
        }
        else {
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
