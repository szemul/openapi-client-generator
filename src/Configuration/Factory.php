<?php

namespace Emul\OpenApiClientGenerator\Configuration;

use Emul\OpenApiClientGenerator\File\FileHandler;

class Factory
{
    public function __construct(private readonly FileHandler $fileHandler)
    {
    }

    public function getConfig(
        string $vendorName,
        string $projectName,
        string $apiDocPath,
        string $clientPath,
        string $rootNamespace
    ): Configuration {
        $composer   = new Composer($vendorName, $projectName);
        $paths      = new Paths($apiDocPath, $clientPath);
        $classPaths = new ClassPaths($rootNamespace);

        return new Configuration($this->fileHandler, $composer, $paths, $classPaths);
    }
}
