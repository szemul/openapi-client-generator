<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

class ClassPaths
{
    private array $apiClasses             = [];
    private array $entityClasses          = [];
    private array $modelClasses           = [];
    private array $actionParameterClasses = [];

    public function __construct(private readonly string $rootNamespace)
    {
    }

    public function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

    public function getApiClasses(): array
    {
        return array_unique($this->apiClasses);
    }

    public function getEntityClasses(): array
    {
        return array_unique($this->entityClasses);
    }

    public function getModelClasses(): array
    {
        return array_unique($this->modelClasses);
    }

    public function getActionParameterClasses(): array
    {
        return $this->actionParameterClasses;
    }

    public function addApiClass(string $apiClass): self
    {
        $this->apiClasses[] = $apiClass;

        return $this;
    }

    public function addEntityClass(string $entityClass): self
    {
        $this->entityClasses[] = $entityClass;

        return $this;
    }

    public function addModelClass(string $modelClass): self
    {
        $this->modelClasses[] = $modelClass;

        return $this;
    }

    public function addActionParameterClass(string $actionParameterClass): self
    {
        $this->actionParameterClasses[] = $actionParameterClass;

        return $this;
    }
}
