<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Carbon\CarbonInterface;
use Emul\OpenApiClientGenerator\PropertyType\Type;
use Emul\OpenApiClientGenerator\Template\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class ModelPropertyTemplateTest extends TestCaseAbstract
{
    private string $rootNamespace = 'Root';
    private string $name          = 'name';

    public function testToStringWhenScalarGiven_shouldGenerateProperty()
    {
        $sut = $this->getSut(Type::string(), true);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var string
             */
            protected string $name;
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    public function testToStringWhenNullableScalarGiven_shouldGenerateProperty()
    {
        $sut = $this->getSut(Type::string(), false);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var string|null
             */
            protected ?string $name;
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    public function testToStringWhenDescriptionSet_shouldGenerateProperty()
    {
        $sut = $this->getSut(Type::string(), true, 'Description of the property');

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var string Description of the property
             */
            protected string $name;
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    public function testToStringWhenObjectGiven_shouldGenerateProperty()
    {
        $sut = $this->getSut(Type::object(CarbonInterface::class), true);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var Carbon\CarbonInterface
             */
            protected \Carbon\CarbonInterface $name;
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    public function testToStringWhenArrayGiven_shouldGenerateProperty()
    {
        $sut = $this->getSut(Type::array(Type::object(CarbonInterface::class)), true);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            /**
             * @var Carbon\CarbonInterface[]
             */
            protected array $name;
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    public function testGetGetterWhenScalarGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(Type::string(), true);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(): string
            {
                return $this->name;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetGetterWhenNullableGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(Type::string(), false);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(bool $throwExceptionIfNotInitialized = false): ?string
            {
                return $this->getPropertyValue('name', $throwExceptionIfNotInitialized);
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetGetterWhenObjectGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(Type::object(CarbonInterface::class), true);

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(): \Carbon\CarbonInterface
            {
                return $this->name;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetGetterWhenArrayGiven_shouldGenerateGetterWithDoc()
    {
        $sut = $this->getSut(Type::array(Type::object(CarbonInterface::class)), false);

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

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetSetterWhenScalarGiven_shouldGenerateSetter()
    {
        $sut = $this->getSut(Type::string(), true);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(string $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $setter);
    }

    public function testGetSetterWhenNullable_shouldGenerateSetter()
    {
        $sut = $this->getSut(Type::string(), false);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(?string $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $setter);
    }

    public function testGetSetterWhenObjectGiven_shouldGenerateSetter()
    {
        $sut = $this->getSut(Type::object(CarbonInterface::class), false);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(?\Carbon\CarbonInterface $name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $setter);
    }

    public function testGetSetterWhenArrayGiven_shouldGenerate()
    {
        $sut = $this->getSut(Type::array(Type::object(CarbonInterface::class)), false);

        $setter         = $sut->getSetter();
        $expectedResult = <<<'EXPECTED'
            public function setName(\Carbon\CarbonInterface ...$name): self
            {
                $this->name = $name;
            
                return $this;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $setter);
    }

    private function getSut(Type $type, bool $isRequired, ?string $description = null): ModelPropertyTemplate
    {
        return new ModelPropertyTemplate($this->rootNamespace, $this->name, $type, $isRequired, $description);
    }
}
