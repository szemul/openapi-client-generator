<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseInterfaceTemplate extends ClassTemplateAbstract
{
    public function __construct(private readonly LocationHelper $locationHelper)
    {
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
        return 'ResponseInterface';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->locationHelper->getModelNamespace()};
            
            interface ResponseInterface
            {
                public function setStatusCode(int \$statusCode): self;
            
                public function getStatusCode(): int;
                
                public function setBody(string \$body): self;
            
                public function getBody(): string;
            }
            TEMPLATE;
    }
}
