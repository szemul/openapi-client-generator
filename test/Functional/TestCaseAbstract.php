<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Functional;

use PHPUnit\Framework\TestCase;

class TestCaseAbstract extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $generatorPath = realpath(__DIR__ . '/../../src/generator.php');
        $apiDocPath    = realpath(__DIR__ . '/data/openapi.json');
        $output        = null;
        $resultCode    = null;
        $command       = 'php ' . $generatorPath
            . ' --api-json-path=' . $apiDocPath
            . ' --client-path=' . self::getTargetPath()
            . ' --vendor-name=shoppinpal'
            . ' --project-name=pet-store-client'
            . ' --root-namespace=PetStoreClient';

        exec($command, $output, $resultCode);

        self::assertSame(0, $resultCode, 'Failed to generate Client');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        exec('rm -rf ' . self::getTargetPath());
    }

    private static function getTargetPath(): string
    {
        return __DIR__ . '/tmp/client/';
    }
}
