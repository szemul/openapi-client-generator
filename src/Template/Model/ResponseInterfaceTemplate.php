<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseInterfaceTemplate extends ClassTemplateAbstract
{
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
        return 'ResponseInterface';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getLocationHelper()->getModelNamespace()};
            
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
