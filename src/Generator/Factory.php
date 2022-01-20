<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use DI\FactoryInterface;

class Factory
{
    private FactoryInterface $diFactory;

    public function __construct(FactoryInterface $diFactory)
    {
        $this->diFactory = $diFactory;
    }

    public function getApiGenerator(): ApiGenerator
    {
        return $this->diFactory->make(ApiGenerator::class);
    }

    public function getActionParameterGenerator(): ActionParameterGenerator
    {
        return $this->diFactory->make(ActionParameterGenerator::class);
    }

    public function getCommonGenerator(): CommonGenerator
    {
        return $this->diFactory->make(CommonGenerator::class);
    }

    public function getExceptionGenerator(): ExceptionGenerator
    {
        return $this->diFactory->make(ExceptionGenerator::class);
    }

    public function getModelGenerator(): ModelGenerator
    {
        return $this->diFactory->make(ModelGenerator::class);
    }
}
