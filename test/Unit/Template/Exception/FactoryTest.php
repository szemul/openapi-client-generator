<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Exception;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Exception\Factory;
use Emul\OpenApiClientGenerator\Template\Exception\PropertyNotInitializedExceptionTemplate;
use Emul\OpenApiClientGenerator\Template\Exception\RequestCodeExceptionTemplate;
use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionPropertyTemplate;
use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Mockery;

class FactoryTest extends TestCaseAbstract
{
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locationHelper = Mockery::mock(LocationHelper::class);
        $this->stringHelper   = Mockery::mock(StringHelper::class);
    }

    public function testGetPropertyNotInitializedExceptionTemplate()
    {
        $result = $this->getSut()->getPropertyNotInitializedExceptionTemplate();

        $this->assertInstanceOf(PropertyNotInitializedExceptionTemplate::class, $result);
    }

    public function testGetRequestCodeExceptionTemplate()
    {
        $result = $this->getSut()->getRequestCodeExceptionTemplate(404);

        $this->assertInstanceOf(RequestCodeExceptionTemplate::class, $result);
    }

    public function testGetRequestExceptionPropertyTemplate()
    {
        $result = $this->getSut()->getRequestExceptionPropertyTemplate('name', PropertyType::int());

        $this->assertInstanceOf(RequestExceptionPropertyTemplate::class, $result);
    }

    public function testGetRequestExceptionTemplate()
    {
        $result = $this->getSut()->getRequestExceptionTemplate();

        $this->assertInstanceOf(RequestExceptionTemplate::class, $result);
    }

    private function getSut(): Factory
    {
        return new Factory($this->locationHelper, $this->stringHelper);
    }
}
