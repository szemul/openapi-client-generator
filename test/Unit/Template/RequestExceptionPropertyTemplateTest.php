<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Template\Exception\RequestExceptionPropertyTemplate;

class RequestExceptionPropertyTemplateTest extends TemplateTestCaseAbstract
{
    private string $name = 'name';

    public function testGetGetterWhenScalarGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::string());

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            public function getName(): ?string
            {
                return $this->getResponseDecoded()['name'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetGetterWhenDescriptionGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::string(), 'This is a description');

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            /**
             * @return string|null   This is a description
             */
            public function getName(): ?string
            {
                return $this->getResponseDecoded()['name'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    public function testGetGetterWhenArrayGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(PropertyType::array(PropertyType::string()));

        $getter         = $sut->getGetter();
        $expectedResult = <<<'EXPECTED'
            /**
             * @return string[]|null   
             */
            public function getName(): ?array
            {
                return $this->getResponseDecoded()['name'] ?? null;
            }
            EXPECTED;

        $this->assertSame($expectedResult, $getter);
    }

    private function getSut(PropertyType $type, ?string $description = null): RequestExceptionPropertyTemplate
    {
        return new RequestExceptionPropertyTemplate($this->locationHelper, $this->stringHelper, $this->name, $type, $description);
    }
}
