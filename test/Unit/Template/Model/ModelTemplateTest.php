<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\ModelTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;
use Mockery;

class ModelTemplateTest extends TemplateTestCaseAbstract
{
    private TypeMapper $typeMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeMapper = new TypeMapper($this->locationHelper, $this->stringHelper);
    }

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
                        return isset($this->key)
                            ? $this->{$propertyName}
                            : null;
                    }
                }
            }
            
            EXPECTED;

        $this->assertSame($expectedResult, $result);
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

    private function getSut(): ModelTemplate
    {
        return new ModelTemplate($this->locationHelper, $this->stringHelper, $this->typeMapper, 'Model');
    }
}
