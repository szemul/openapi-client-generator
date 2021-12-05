<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Template\Model\ResponseListInterfaceTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ResponseListInterfaceTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldGenerateClass()
    {
        $sut = $this->getSut();

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php

            declare(strict_types=1);

            namespace Root\Model;

            interface ResponseListInterface
            {
                public function getItemClass(): string;
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Model/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Model\ResponseListInterface', $className);
    }

    private function getSut(): ResponseListInterfaceTemplate
    {
        return new ResponseListInterfaceTemplate($this->locationHelper, $this->stringHelper);
    }
}
