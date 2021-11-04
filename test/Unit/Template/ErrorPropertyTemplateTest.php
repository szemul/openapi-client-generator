<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template;

use Carbon\CarbonInterface;
use Emul\OpenApiClientGenerator\PropertyType\Type;
use Emul\OpenApiClientGenerator\Template\ErrorPropertyTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class ErrorPropertyTemplateTest extends TestCaseAbstract
{
    private string $rootNamespace = 'Root';
    private string $name          = 'name';

    public function testGetGetterWhenScalarGiven_shouldGenerateGetter()
    {
        $sut = $this->getSut(Type::string());

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
        $sut = $this->getSut(Type::string(), 'This is a description');

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
        $sut = $this->getSut(Type::array(Type::string()));

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

    private function getSut(Type $type, ?string $description = null): ErrorPropertyTemplate
    {
        return new ErrorPropertyTemplate($this->rootNamespace, $this->name, $type, $description);
    }
}
