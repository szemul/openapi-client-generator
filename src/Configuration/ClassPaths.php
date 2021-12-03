<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

class ClassPaths
{
    private string $rootNamespace;
    private array  $apiClasses    = [];
    private array  $entityClasses = [];
    private array  $modelClasses  = [];

    public function __construct(string $rootNamespace)
    {
        $this->rootNamespace = $rootNamespace;
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

}
