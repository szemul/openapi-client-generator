<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

class Paths
{
    private string $apiDocPath;
    private string $targetRootPath;
    private string $srcPath;

    public function __construct(string $apiDocPath, string $targetRootPath)
    {
        $this->apiDocPath     = $apiDocPath;
        $this->targetRootPath = rtrim($targetRootPath, '/') . '/';
        $this->srcPath        = $this->targetRootPath . 'src/';
    }

    public function getApiDocPath(): string
    {
        return $this->apiDocPath;
    }

    public function getTargetRootPath(): string
    {
        return $this->targetRootPath;
    }

    public function getSrcPath(): string
    {
        return $this->srcPath;
    }
}
