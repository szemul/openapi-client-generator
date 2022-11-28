<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Functional;

use PHPUnit\Framework\TestCase;

class CodeComparisonTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::cleanUp();
        self::runGenerator();
    }

    public function test()
    {
        $this->assertTrue(true);
    }

    private static function runGenerator(): void
    {
        $generatorPath = realpath(__DIR__ . '/../../src/generator.php');
        $apiDocPath    = realpath(__DIR__ . '/data/openapi.json');
        $output        = null;
        $resultCode    = null;
        $command       = 'php ' . $generatorPath
            . ' --api-json-path=' . $apiDocPath
            . ' --client-path=' . self::getTargetPath()
            . ' --vendor-name=emulgeator'
            . ' --project-name=test'
            . ' --root-namespace=Test';

        exec($command, $output, $resultCode);

        self::assertSame(0, $resultCode);
    }

    private static function cleanUp(): void
    {
        exec('rm -rf ' . self::getTargetPath());
    }

    private static function getTargetPath(): string
    {
        return __DIR__ . '/tmp/client/';
    }
}
