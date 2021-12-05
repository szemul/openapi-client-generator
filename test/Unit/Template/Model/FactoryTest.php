<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\EnumTemplate;
use Emul\OpenApiClientGenerator\Template\Model\Factory;
use Emul\OpenApiClientGenerator\Template\Model\ModelAbstractTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ModelTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ResponseListInterfaceTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ResponseListTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
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
        $this->stringHelper   = new StringHelper();
    }

    public function testModelAbstractTemplate()
    {
        $result = $this->getSut()->getModelAbstractTemplate();

        $this->assertInstanceOf(ModelAbstractTemplate::class, $result);
    }

    public function testModelTemplate()
    {
        $result = $this->getSut()->getModelTemplate('model');

        $this->assertInstanceOf(ModelTemplate::class, $result);
    }

    public function testModelPropertyTemplate()
    {
        $result = $this->getSut()->getModelPropertyTemplate('property', PropertyType::int(), true);

        $this->assertInstanceOf(ModelPropertyTemplate::class, $result);
    }

    public function testEnumTemplate()
    {
        $result = $this->getSut()->getEnumTemplate('Enum');

        $this->assertInstanceOf(EnumTemplate::class, $result);
    }

    public function testResponseListInterfaceTemplate()
    {
        $result = $this->getSut()->getResponseListInterfaceTemplate();

        $this->assertInstanceOf(ResponseListInterfaceTemplate::class, $result);
    }

    public function testResponseListTemplate()
    {
        $result = $this->getSut()->getResponseListTemplate('Item');

        $this->assertInstanceOf(ResponseListTemplate::class, $result);
    }

    private function getSut(): Factory
    {
        return new Factory($this->typeMapper, $this->locationHelper, $this->stringHelper);
    }
}
