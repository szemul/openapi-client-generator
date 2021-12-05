<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Exception;

use Emul\OpenApiClientGenerator\Template\Exception\PropertyNotInitializedExceptionTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class PropertyNotInitializedExceptionTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldRenderProperly()
    {
        $expectedValue = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Root\Exception;
            
            use Exception;
            
            class PropertyNotInitializedException extends Exception
            {
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedValue, (string)$this->getSut());
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Exception/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Exception\PropertyNotInitializedException', $className);
    }

    private function getSut(): PropertyNotInitializedExceptionTemplate
    {
        return new PropertyNotInitializedExceptionTemplate($this->locationHelper, $this->stringHelper);
    }
}
