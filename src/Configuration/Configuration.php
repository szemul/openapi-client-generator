<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

use Emul\OpenApiClientGenerator\File\FileHandler;

class Configuration
{
    private FileHandler $fileHandler;
    private Composer    $composer;
    private Paths       $paths;
    private ClassPaths  $classPaths;
    private array       $apiDoc;

    public function __construct(
        FileHandler $fileHandler,
        Composer $composer,
        Paths $paths,
        ClassPaths $classPaths
    ) {
        $this->fileHandler = $fileHandler;
        $this->paths       = $paths;
        $this->composer    = $composer;
        $this->classPaths  = $classPaths;
        $this->apiDoc      = $this->getDecodedApiDoc();
    }

    public function getComposer(): Composer
    {
        return $this->composer;
    }

    public function getPaths(): Paths
    {
        return $this->paths;
    }

    public function getClassPaths(): ClassPaths
    {
        return $this->classPaths;
    }

    public function getApiDoc(): array
    {
        return $this->apiDoc;
    }

    private function getDecodedApiDoc(): array
    {
        return json_decode($this->fileHandler->getFileContent($this->paths->getApiDocPath()), true);
    }
}
