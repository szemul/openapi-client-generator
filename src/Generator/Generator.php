<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\CommandHelper;
use Symfony\Component\Yaml\Yaml;

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
        echo 'Generating Client ...' . PHP_EOL;
        foreach ($this->getGenerators() as $generator) {
            $generator->generate();
        }

        $this->checkSyntax();
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

    private function checkSyntax()
    {
        echo 'Validating syntax of generated code ...' . PHP_EOL;
        $iterator = new \RecursiveDirectoryIterator($this->configuration->getPaths()->getTargetRootPath());

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            $resultCode = null;
            if ($file->getExtension() == 'php') {
                $result = exec('php -l ' . $file->getRealPath(), result_code: $resultCode);

                if ($resultCode !== 0) {
                    throw new \RuntimeException($result);
                }
            }
        }
    }

    private function fixCodingStandards()
    {
        echo 'Fixing coding standard ...' . PHP_EOL;
        $command = ROOT . '/vendor/bin/php-cs-fixer --config='
            . ROOT . '/.php-cs-fixer.generated.php fix '
            . $this->configuration->getPaths()->getTargetRootPath();

        $this->commandHelper->execute($command);
    }

    private function copyDocumentation(): void
    {
        echo 'Copying documentation ...' . PHP_EOL;
        $destinationDirectory = $this->configuration->getPaths()->getTargetRootPath() . 'doc/';
        $yaml                 = Yaml::dump($this->configuration->getApiDoc(), 10);

        $this->fileHandler->createDirectory($destinationDirectory);
        $this->fileHandler->saveFile($destinationDirectory . 'openapi.yaml', $yaml);
    }

    private function copyGitIgnore(): void
    {
        echo 'Copying git ignore file ...' . PHP_EOL;

        $this->fileHandler->copyFile(
            ROOT . '/.gitignore',
            $this->configuration->getPaths()->getTargetRootPath() . '.gitignore'
        );
    }
}
