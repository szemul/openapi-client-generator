<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Emul\OpenApiClientGenerator\Configuration\ClassPaths;
use Emul\OpenApiClientGenerator\Configuration\Composer;
use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Configuration\Paths;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Mockery;

class TemplateTestCaseAbstract extends TestCaseAbstract
{
    protected FileHandler    $fileHandler;
    protected LocationHelper $locationHelper;
    protected StringHelper   $stringHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileHandler = Mockery::mock(FileHandler::class);

        $this->expectApiDocRead();

        $this->locationHelper = new LocationHelper(
            new Configuration(
                $this->fileHandler,
                new Composer('Vendor', 'Project'),
                new Paths('/api', '/'),
                new ClassPaths('Root')
            )
        );
        $this->stringHelper   = new StringHelper();
    }

    private function expectApiDocRead()
    {
        $this->fileHandler
            ->shouldReceive('getFileContent')
            ->once()
            ->andReturn('[]');
    }
}