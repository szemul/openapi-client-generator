<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

class Generator
{
    private string $apiDocPath;
    private string $targetRootPath;
    private string $rootNamespace;

    public function __construct(string $apiDocPath, string $targetRootPath, string $rootNamespace)
    {
        $this->apiDocPath     = $apiDocPath;
        $this->targetRootPath = $targetRootPath;
        $this->rootNamespace  = $rootNamespace;
    }

    public function generate(): void
    {
        $apiDoc               = $this->getDecodedApiDoc();
        $modelGenerator = new ModelGenerator($apiDoc, $this->targetRootPath, $this->rootNamespace);

        $modelGenerator->generate();
    }

    private function getDecodedApiDoc(): array
    {
        return json_decode(file_get_contents($this->apiDocPath), true);
    }
}
