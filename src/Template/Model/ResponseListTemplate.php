<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseListTemplate extends ClassTemplateAbstract
{

    public function __construct(
        LocationHelper          $locationHelper,
        StringHelper            $stringHelper,
        private readonly string $itemClassName
    ) {
        parent::__construct($locationHelper, $stringHelper);
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
        return $this->itemClassName . 'List';
    }

    public function __toString(): string
    {
        return <<<ENUM
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getLocationHelper()->getModelNamespace()};
            
            use JsonSerializable;
            
            class {$this->getClassName()} implements ResponseListInterface, JsonSerializable
            {
                /** @var {$this->itemClassName}[] */
                private array \$items = [];
            
                public function getItemClass(): string
                {
                    return {$this->itemClassName}::class;
                }
            
                public function add({$this->itemClassName} \$item): self
                {
                    \$this->items[] = \$item;
            
                    return \$this;
                }
            
                /**
                 * @return {$this->itemClassName}[]
                 */
                public function getItems(): array
                {
                    return \$this->items;
                }
                
                public function jsonSerialize(): mixed
                {
                    return \$this->items;
                }
            }
            ENUM;
    }
}
