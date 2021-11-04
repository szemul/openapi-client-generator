<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

abstract class TemplateAbstract
{
    private string $rootNamespace;

    public function __construct(string $rootNamespace)
    {
        $this->rootNamespace = $rootNamespace;
    }

    protected function getModelNamespace(): string
    {
        return $this->rootNamespace . '\\Model';
    }

    protected function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

}
