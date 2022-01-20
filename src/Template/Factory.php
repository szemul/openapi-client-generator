<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use DI\FactoryInterface;
use Emul\OpenApiClientGenerator\Template\Api\Factory as ApiTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Common\Factory as CommonTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Exception\Factory as ExceptionTemplateFactory;
use Emul\OpenApiClientGenerator\Template\Model\Factory as ModelTemplateFactory;

class Factory
{
    private FactoryInterface $diFactory;

    public function __construct(FactoryInterface $diFactory)
    {
        $this->diFactory = $diFactory;
    }

    public function getApiFactory(): ApiTemplateFactory
    {
        return $this->diFactory->make(ApiTemplateFactory::class);
    }

    public function getCommonFactory(): CommonTemplateFactory
    {
        return $this->diFactory->make(CommonTemplateFactory::class);
    }

    public function getExceptionFactory(): ExceptionTemplateFactory
    {
        return $this->diFactory->make(ExceptionTemplateFactory::class);
    }

    public function getModelFactory(): ModelTemplateFactory
    {
        return $this->diFactory->make(ModelTemplateFactory::class);
    }
}
