<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Template\Model\ModelAbstractTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ModelAbstractTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldGenerateClass()
    {
        $sut = $this->getSut();

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Root\Model;
            
            use Root\JsonSerializableTrait;
            use Root\Exception\PropertyNotInitializedException;
            use JsonSerializable;
            use ReflectionException;
            use ReflectionProperty;
            
            abstract class ModelAbstract implements JsonSerializable
            {
                use JsonSerializableTrait;
            
                /**
                 * @throws PropertyNotInitializedException
                 * @throws ReflectionException
                 */
                protected function getPropertyValue(string $propertyName, bool $throwExceptionIfNotInitialized)
                {
                    if ($throwExceptionIfNotInitialized) {
                        $propertyReflection = new ReflectionProperty($this, $propertyName);
            
                        if (!$propertyReflection->isInitialized($this)) {
                            throw new PropertyNotInitializedException();
                        }
            
                        return $this->{$propertyName};
                    } else {
                        return isset($this->{$propertyName})
                            ? $this->{$propertyName}
                            : null;
                    }
                }
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

        $this->assertSame('Root\Model\ModelAbstract', $className);
    }

    private function getSut(): ModelAbstractTemplate
    {
        return new ModelAbstractTemplate($this->locationHelper);
    }
}
