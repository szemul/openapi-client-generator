<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use DI\FactoryInterface;
use Emul\OpenApiClientGenerator\Configuration\Configuration;

class Factory
{

    public function __construct(private readonly FactoryInterface $diFactory)
    {
    }

    public function getGenerator(Configuration $config): Generator
    {
        return $this->diFactory->make(Generator::class, ['configuration' => $config]);
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
