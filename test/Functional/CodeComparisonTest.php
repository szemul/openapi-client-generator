<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Functional;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class CodeComparisonTest extends TestCase
{
    private const EXPECTED_CLIENT_PATH = __DIR__ . '/data/client/';
    private const GENERATED_CLIENT_PATH = __DIR__ . '/tmp/client/';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::cleanUp();
        self::runGenerator();
    }

    /**
     * Really fragile and far from an optimal test. The reason  of this test is try to guarantee some safety.
     * If the files differ please check what's different, if the difference is intended please just update the expected code.
     */
    public function test()
    {
        $iterator = new RecursiveDirectoryIterator(self::EXPECTED_CLIENT_PATH . 'src', RecursiveDirectoryIterator::SKIP_DOTS);

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            $relativePath = str_replace(self::EXPECTED_CLIENT_PATH, '',  $file->getRealPath());

            $this->assertFileEquals(
                $file->getRealPath(),
                self::GENERATED_CLIENT_PATH . $relativePath,
                "File \033[31m$relativePath\033[0m is different than expected"
            );
        }

        $this->assertTrue(true);
    }

    private static function runGenerator(): void
    {
        $generatorPath = realpath(__DIR__ . '/../../src/generator.php');
        $apiDocPath    = realpath(self::EXPECTED_CLIENT_PATH . 'doc/openapi.json');
        $output        = null;
        $resultCode    = null;
        $command       = 'php ' . $generatorPath
            . ' --api-json-path=' . $apiDocPath
            . ' --client-path=' . self::GENERATED_CLIENT_PATH
            . ' --vendor-name=emulgeator'
            . ' --project-name=test'
            . ' --root-namespace=Test';

        exec($command, $output, $resultCode);

        self::assertSame(0, $resultCode);
    }

    private static function cleanUp(): void
    {
        exec('rm -rf ' . self::GENERATED_CLIENT_PATH);
    }
}
