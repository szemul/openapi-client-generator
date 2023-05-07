<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Template\Common\JsonSerializableTraitTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class JsonSerializableTraitTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString()
    {
        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root;
            
            use Carbon\CarbonInterface;
            
            trait JsonSerializableTrait
            {
                public function jsonSerialize(): mixed
                {
                    $properties = get_object_vars($this);
            
                    foreach ($properties as $index => $property) {
                        if ($property instanceof CarbonInterface) {
                            $properties[$index] = $property->toIso8601ZuluString();
                        }
                    }
            
                    return $properties;
                }
            
                public function toArray(): array
                {
                    return json_decode(json_encode($this), true);
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\JsonSerializableTrait', $className);
    }

    private function getSut(): JsonSerializableTraitTemplate
    {
        return new JsonSerializableTraitTemplate($this->locationHelper);
    }
}
