<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

use Emul\OpenApiClientGenerator\File\FileHandler;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private array $apiDoc;

    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly Composer $composer,
        private readonly Paths $paths,
        private readonly ClassPaths $classPaths
    ) {
        $this->apiDoc = $this->getDecodedApiDoc();
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
        $fileContent = $this->fileHandler->getFileContent($this->paths->getApiDocPath());

        return Yaml::parse($fileContent);
    }
}
