<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Carbon\CarbonInterface;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;
use Mockery;

class ModelPropertyTemplateTest extends TemplateTestCaseAbstract
{
    private string $name = 'name';

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeMapper = Mockery::mock(TypeMapper::class);
    }

    public function testToStringWhenScalarGiven_shouldGenerateProperty()
    {
        $sut = $this->getSut(PropertyType::string(), true);

        $this->expectPropertyMappedToPhpType($sut, 'string');
        $this->expectPropertyMappedToDocType($sut, 'docString');

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var docString
             */
            protected string $name;
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testToStringWhenDescriptionSet_shouldGenerateProperty()
    {
        $sut = $this->getSut(PropertyType::string(), true, 'Description of the property');

        $this->expectPropertyMappedToPhpType($sut, 'string');
        $this->expectPropertyMappedToDocType($sut, 'docString');

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var docString Description of the property
             */
            protected string $name;
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetGetterWhenScalarGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::string(), true);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(): string
            {
                return $this->name;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $getter);
    }

    public function testGetGetterWhenNullableGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::string(), false);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(bool $throwExceptionIfNotInitialized = false): ?string
            {
                return $this->getPropertyValue('name', $throwExceptionIfNotInitialized);
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $getter);
    }

    public function testGetGetterWhenObjectGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::object(CarbonInterface::class), true);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(): CarbonInterface
            {
                return $this->name;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $getter);
    }

    public function testGetGetterWhenArrayGiven_shouldGenerateGetterWithDoc()
    {
        $type = PropertyType::array(PropertyType::object(CarbonInterface::class));
        $sut  = $this->getSut($type, false);

        $this->expectArrayItemTypeRetrieved($type, 'Carbon\CarbonInterface');

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            /**
             * @return Carbon\CarbonInterface[]|null
             */
            public function getName(bool $throwExceptionIfNotInitialized = false): ?array
            {
                return $this->getPropertyValue('name', $throwExceptionIfNotInitialized);
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $getter);
    }

    public function testGetSetterWhenScalarGiven_shouldGenerateSetter()
    {
        $sut = $this->getSut(PropertyType::string(), true);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(string $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $setter);
    }

    public function testGetSetterWhenNullable_shouldGenerateSetter()
    {
        $sut = $this->getSut(PropertyType::string(), false);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(?string $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $setter);
    }

    public function testGetSetterWhenObjectGiven_shouldGenerateSetter()
    {
        $sut = $this->getSut(PropertyType::object(CarbonInterface::class), false);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(?CarbonInterface $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $setter);
    }

    public function testGetSetterWhenArrayGiven_shouldGenerate()
    {
        $type = PropertyType::array(PropertyType::object(CarbonInterface::class));
        $sut  = $this->getSut($type, false);

        $this->expectArrayItemTypeRetrieved($type, 'CarbonInterface');

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(CarbonInterface ...$name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $setter);
    }

    private function expectPropertyMappedToPhpType(ModelPropertyTemplate $property, string $expectedResult): void
    {
        $this->typeMapper
            ->shouldReceive('mapModelPropertyTemplateToPhp')
            ->once()
            ->with($property)
            ->andReturn($expectedResult);
    }

    private function expectPropertyMappedToDocType(ModelPropertyTemplate $property, string $expectedResult): void
    {
        $this->typeMapper
            ->shouldReceive('mapModelPropertyTemplateToDoc')
            ->once()
            ->with($property)
            ->andReturn($expectedResult);
    }

    private function expectArrayItemTypeRetrieved(PropertyType $type, string $expectedResult): void
    {
        $this->typeMapper
            ->shouldReceive('getArrayItemType')
            ->once()
            ->with($type)
            ->andReturn($expectedResult);
    }

    private function getSut(PropertyType $type, bool $isRequired, ?string $description = null): ModelPropertyTemplate
    {
        return new ModelPropertyTemplate(
            $this->typeMapper,
            $this->stringHelper,
            $this->name,
            $type,
            $isRequired,
            $description
        );
    }
}
