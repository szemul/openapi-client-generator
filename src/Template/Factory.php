<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Api\Factory as ApiTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Common\Factory as CommonTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Exception\Factory as ExceptionTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Model\Factory as ModelTemplateFactory;

class Factory
{
    private TypeMapper     $typeMapper;
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    public function __construct(TypeMapper $typeMapper, LocationHelper $locationHelper, StringHelper $stringHelper)
    {
        $this->typeMapper     = $typeMapper;
        $this->locationHelper = $locationHelper;
        $this->stringHelper   = $stringHelper;
    }

    public function getApiFactory(): ApiTemplateFactory
    {
        return new ApiTemplateFactory($this->locationHelper, $this->stringHelper);
    }

    public function getCommonFactory(): CommonTemplateFactory
    {
        return new CommonTemplateFactory($this->locationHelper, $this->stringHelper);
    }

    public function getExceptionFactory(): ExceptionTemplateFactory
    {
        return new ExceptionTemplateFactory($this->locationHelper, $this->stringHelper);
    }

    public function getModelFactory(): ModelTemplateFactory
    {
        return new ModelTemplateFactory($this->typeMapper, $this->locationHelper, $this->stringHelper);
    }
}
