<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\CommandHelper;

class Generator
{
    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly Configuration $configuration,
        private readonly Factory $factory,
        private readonly CommandHelper $commandHelper
    ) {
        $this->fileHandler->createDirectory($this->configuration->getPaths()->getSrcPath());
    }

    public function generate(): void
    {
        foreach ($this->getGenerators() as $generator) {
            $generator->generate();
        }

        $this->fixCodingStandards();
        $this->copyDocumentation();
        $this->copyGitIgnore();
    }

    /**
     * @return GeneratorInterface[]
     */
    private function getGenerators(): array
    {
        return [
            $this->factory->getModelGenerator(),
            $this->factory->getExceptionGenerator(),
            $this->factory->getActionParameterGenerator(),
            $this->factory->getApiGenerator(),
            $this->factory->getCommonGenerator(),
        ];
    }

    private function fixCodingStandards()
    {
        $command = ROOT . '/vendor/bin/php-cs-fixer --config='
            . ROOT . '/.php-cs-fixer.generated.php fix '
            . $this->configuration->getPaths()->getTargetRootPath();

        $this->commandHelper->execute($command);
    }

    private function copyDocumentation(): void
    {
        $destinationDirectory = $this->configuration->getPaths()->getTargetRootPath() . 'doc/';

        $this->fileHandler->createDirectory($destinationDirectory);
        $this->fileHandler->copyFile(
            $this->configuration->getPaths()->getApiDocPath(),
            $destinationDirectory . 'openapi.json'
        );
    }

    private function copyGitIgnore(): void
    {
        $this->fileHandler->copyFile(
            ROOT . '/.gitignore',
            $this->configuration->getPaths()->getTargetRootPath() . '.gitignore'
        );
    }
}
