<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class RequestExceptionTemplate extends ClassTemplateAbstract
{
    public function __construct(private readonly LocationHelper $locationHelper)
    {
    }

    public function __toString(): string
    {
        return <<<MODEL
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Carbon\CarbonInterface;
            use Exception;
            
            class {$this->getClassName()} extends Exception
            {
                private string \$responseBody;
                private array  \$responseHeaders = [];
            
                public function __construct(int \$code, string \$responseBody, array \$responseHeaders)
                {
                    parent::__construct('Received ' . \$code, \$code);
            
                    \$this->responseBody    = \$responseBody;
                    \$this->responseHeaders = \$responseHeaders;
                }
            
                public function getResponseBody(): string
                {
                    return \$this->responseBody;
                }
            
                public function getResponseDecoded()
                {
                    return json_decode(\$this->getResponseBody(), true);
                }
            
                public function getResponseHeaders(): array
                {
                    return \$this->responseHeaders;
                }
            }
            MODEL;
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getExceptionPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getExceptionNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'RequestException';
    }
}
