<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Exception;

use Carbon\CarbonInterface;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class RequestExceptionPropertyTemplateTest extends TemplateTestCaseAbstract
{
    private string $name = 'property';

    public function testGetGetterWhenArrayOfScalarGiven_shouldGenerateAccordingly()
    {
        $type   = PropertyType::array(PropertyType::int());
        $sut    = $this->getSut($type);
        $getter = $sut->getGetter();

        $expectedValue = <<<'EXPECTED'
            /**
             * @return int[]|null   
             */
            public function getProperty(): ?array
            {
                return $this->getResponseDecoded()['property'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedValue, $getter);
    }

    public function testGetGetterWhenArrayOfObjectsGiven_shouldGenerateAccordingly()
    {
        $type   = PropertyType::array(PropertyType::object(CarbonInterface::class));
        $sut    = $this->getSut($type);
        $getter = $sut->getGetter();

        $expectedValue = <<<'EXPECTED'
            /**
             * @return Carbon\CarbonInterface[]|null   
             */
            public function getProperty(): ?array
            {
                return $this->getResponseDecoded()['property'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedValue, $getter);
    }

    public function testGetGetterWhenScalarGiven_shouldGenerateAccordingly()
    {
        $type   = PropertyType::int();
        $sut    = $this->getSut($type);
        $getter = $sut->getGetter();

        $expectedValue = <<<'EXPECTED'
            public function getProperty(): ?int
            {
                return $this->getResponseDecoded()['property'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedValue, $getter);
    }

    public function testGetGetterWhenScalarWithDescriptionGiven_shouldGenerateAccordingly()
    {
        $type   = PropertyType::int();
        $sut    = $this->getSut($type, 'Property description');
        $getter = $sut->getGetter();

        $expectedValue = <<<'EXPECTED'
            /**
             * @return int|null   Property description
             */
            public function getProperty(): ?int
            {
                return $this->getResponseDecoded()['property'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedValue, $getter);
    }

    private function getSut(PropertyType $type, ?string $description = null): RequestExceptionPropertyTemplate
    {
        return new RequestExceptionPropertyTemplate($this->locationHelper, $this->stringHelper, $this->name, $type, $description);
    }
}
