<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Factory;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Emul\OpenApiClientGenerator\Template\Api\Factory as ApiFactory;
use Emul\OpenApiClientGenerator\Template\Common\Factory as CommonFactory;
use Emul\OpenApiClientGenerator\Template\Exception\Factory as ExceptionFactory;
use Emul\OpenApiClientGenerator\Template\Model\Factory as ModelFactory;
use Mockery;

class FactoryTest extends TestCaseAbstract
{
    private TypeMapper     $typeMapper;
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeMapper     = Mockery::mock(TypeMapper::class);
        $this->locationHelper = Mockery::mock(LocationHelper::class);
        $this->stringHelper   = Mockery::mock(StringHelper::class);
    }

    public function testGetApiFactory()
    {
        $factory = $this->getSut()->getApiFactory();

        $this->assertInstanceOf(ApiFactory::class, $factory);
    }

    public function testGetCommonFactory()
    {
        $factory = $this->getSut()->getCommonFactory();

        $this->assertInstanceOf(CommonFactory::class, $factory);
    }

    public function testGetExceptionFactory()
    {
        $factory = $this->getSut()->getExceptionFactory();

        $this->assertInstanceOf(ExceptionFactory::class, $factory);
    }

    public function testGetModelFactory()
    {
        $factory = $this->getSut()->getModelFactory();

        $this->assertInstanceOf(ModelFactory::class, $factory);
    }

    private function getSut(): Factory
    {
        return new Factory($this->typeMapper, $this->locationHelper, $this->stringHelper);
    }
}
