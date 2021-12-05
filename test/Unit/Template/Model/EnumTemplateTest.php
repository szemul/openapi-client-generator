<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Model\EnumTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class EnumTemplateTest extends TemplateTestCaseAbstract
{

    public function testToString_shouldGenerateClassProperly()
    {
        $sut = $this->getSut('number', 'one', 'two');

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root\Model\Enum;
            
            use Emul\Enum\EnumAbstract;
            
            class Number extends EnumAbstract
            {
                public const ONE = 'one';
                public const TWO = 'two';
                public static function one(): self
                {
                    return new self(self::ONE);
                }
                public static function two(): self
                {
                    return new self(self::TWO);
                }
                protected static function getPossibleValues(): array
                {
                    return [self::ONE, self::TWO];
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut('enum')->getDirectory();

        $this->assertSame('/src/Model/Enum/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut('enum')->getClassName(true);

        $this->assertSame('Root\Model\Enum\Enum', $className);
    }

    private function getSut(string $enumName, string ...$values): EnumTemplate
    {
        return new EnumTemplate($this->locationHelper, new StringHelper(), $enumName, ...$values);
    }
}
