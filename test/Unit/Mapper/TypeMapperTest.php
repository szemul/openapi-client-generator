<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Mapper;

use Emul\OpenApiClientGenerator\Configuration\Configuration;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class TypeMapperTest extends TestCaseAbstract
{
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = \Mockery::mock(Configuration::class);
    }

    public function testGetArrayItemType_whenScalarGiven_shouldReturnNull()
    {
        $result = $this->getSut()->getArrayItemType(PropertyType::string());

        $this->assertNull($result);
    }

    public function testGetArrayItemType_whenArrayWithoutType_shouldReturnNull()
    {
        $result = $this->getSut()->getArrayItemType(PropertyType::array(null));

        $this->assertNull($result);
    }

    public function testGetArrayItemType_whenArrayOfScalars_shouldReturnScalarType()
    {
        $result = $this->getSut()->getArrayItemType(PropertyType::array(PropertyType::string()));

        $this->assertSame('string', $result);
    }

    public function testGetArrayItemType_whenArrayOfArrays_shouldReturnNull()
    {
        $result = $this->getSut()->getArrayItemType(
            PropertyType::array(PropertyType::array(PropertyType::string()))
        );

        $this->assertNull($result);
    }

    public function testGetArrayItemType_whenArrayOfObjects_shouldReturnClassName()
    {
        $result = $this->getSut()->getArrayItemType(
            PropertyType::array(PropertyType::object('Class'))
        );

        $this->assertSame('\\Class', $result);
    }

    private function getSut(): TypeMapper
    {
        $stringHelper = new StringHelper();

        return new TypeMapper(
            $this->configuration,
            new LocationHelper($this->configuration),
            $stringHelper,
            new ClassHelper($stringHelper)
        );
    }
}
