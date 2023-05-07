<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseListTemplate extends ClassTemplateAbstract
{
    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly string         $itemClassName
    ) {
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getModelPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getModelNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->itemClassName . 'List';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->locationHelper->getModelNamespace()};
            
            use JsonSerializable;
            
            class {$this->getClassName()} implements ResponseListInterface, ResponseInterface, JsonSerializable
            {
                use ResponseTrait;
            
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
            TEMPLATE;
    }
}
