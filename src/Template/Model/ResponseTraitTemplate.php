<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ResponseTraitTemplate extends ClassTemplateAbstract
{
    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper
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
        return 'ResponseTrait';
    }

    public function __toString(): string
    {
        return <<<TEMPLATE
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getLocationHelper()->getModelNamespace()};
            
            use JsonSerializable;
            
            trait {$this->getClassName()}
            {
                private int \$statusCode;
                private string \$body;
            
                public function setStatusCode(int \$statusCode): self
                {
                    \$this->statusCode = \$statusCode;
                    
                    return \$this;
                }
            
                public function getStatusCode(): int
                {
                    return \$this->statusCode;
                }
                
                public function setBody(string \$body): self
                {
                    \$this->body = \$body;
                    
                    return \$this;
                }
            
                public function getBody(): string
                {
                    return \$this->body;
                }
            }
            TEMPLATE;
    }
}
