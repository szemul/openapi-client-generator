<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Emul\OpenApiClientGenerator\Configuration\ClassPaths;
use Emul\OpenApiClientGenerator\Configuration\Composer;
use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Configuration\Paths;
use Emul\OpenApiClientGenerator\File\FileHandler;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Mockery;

class TemplateTestCaseAbstract extends TestCaseAbstract
{
    protected FileHandler    $fileHandler;
    protected Configuration  $configuration;
    protected LocationHelper $locationHelper;
    protected StringHelper   $stringHelper;
    protected ClassHelper    $classHelper;
    protected TypeMapper     $typeMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileHandler = Mockery::mock(FileHandler::class);

        $this->expectApiDocRead();

        $this->configuration  = new Configuration(
            $this->fileHandler,
            new Composer('Vendor', 'Project'),
            new Paths('/api', '/'),
            new ClassPaths('Root')
        );
        $this->locationHelper = new LocationHelper($this->configuration);
        $this->stringHelper   = new StringHelper();
        $this->classHelper    = new ClassHelper($this->stringHelper);
        $this->typeMapper     = new TypeMapper($this->configuration, $this->locationHelper, $this->stringHelper, $this->classHelper);
    }

    private function expectApiDocRead()
    {
        $this->fileHandler
            ->shouldReceive('getFileContent')
            ->once()
            ->andReturn('[]');
    }
}
