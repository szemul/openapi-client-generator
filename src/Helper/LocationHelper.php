<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

use Emul\OpenApiClientGenerator\Configuration\Configuration;

class LocationHelper
{
    const NAME_API              = 'Api';
    const NAME_EXCEPTION        = 'Exception';
    const NAME_MODEL            = 'Model';
    const NAME_ENUM             = 'Enum';
    const NAME_ACTION_PARAMETER = 'ActionParameter';

    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function getApiPath(): string
    {
        return $this->getPath(self::NAME_API);
    }

    public function getExceptionPath(): string
    {
        return $this->getPath(self::NAME_EXCEPTION);
    }

    public function getModelPath(): string
    {
        return $this->getPath(self::NAME_MODEL);
    }

    public function getActionParameterPath(): string
    {
        return $this->getPath(self::NAME_MODEL) . self::NAME_ACTION_PARAMETER . '/';
    }

    public function getEnumPath(): string
    {
        return $this->getPath(self::NAME_MODEL) . self::NAME_ENUM . '/';
    }

    public function getRootPath(): string
    {
        return $this->configuration->getPaths()->getSrcPath();
    }

    public function getApiNamespace(): string
    {
        return $this->getNamespace(self::NAME_API);
    }

    public function getExceptionNamespace(): string
    {
        return $this->getNamespace(self::NAME_EXCEPTION);
    }

    public function getModelNamespace(): string
    {
        return $this->getNamespace(self::NAME_MODEL);
    }

    public function getActionParameterNamespace(): string
    {
        return $this->getNamespace(self::NAME_MODEL) . '\\' . self::NAME_ACTION_PARAMETER;
    }

    public function getEnumNamespace(): string
    {
        return $this->getNamespace(self::NAME_MODEL) . '\\' . self::NAME_ENUM;
    }

    public function getRootNamespace(): string
    {
        return $this->configuration->getClassPaths()->getRootNamespace();
    }

    public function getEscapedRootNamespace(): string
    {
        return addcslashes($this->getRootNamespace(), '\\');
    }

    private function getPath(string $subDirectory): string
    {
        return $this->configuration->getPaths()->getSrcPath() . $subDirectory . '/';
    }

    private function getNamespace(string $subNamespace): string
    {
        return $this->configuration->getClassPaths()->getRootNamespace() . '\\' . $subNamespace;
    }
}
