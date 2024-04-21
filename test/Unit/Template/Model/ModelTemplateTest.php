<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ModelTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ModelTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldGenerateClass()
    {
        $properties = [
            $this->getPropertyTemplate('property1', PropertyType::int(), false, 'First'),
            $this->getPropertyTemplate('property2', PropertyType::array(PropertyType::string()), true, 'Second'),
        ];
        $sut        = $this->getSut(false, ...$properties);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root\Model;
            
            class Model extends ModelAbstract 
            {
                /**
                 * @var int First
                 */
                protected int $property1;

                /**
                 * @var string[] Second
                 */
                protected ?array $property2;

                /**
                 * @param string[] $property2
                 */
                public function __construct(int $property1, ?array $property2 = null)
                {
                    $this->property1 = $property1;
                    $this->property2 = $property2;
                }

                public function getProperty1(): int
                {
                    return $this->property1;
                }

                /**
                 * @return string[]|null
                 */
                public function getProperty2(bool $throwExceptionIfNotInitialized = false): ?array
                {
                    return $this->getPropertyValue('property2', $throwExceptionIfNotInitialized);
                }
            
                public function setProperty1(int $property1): self
                {
                    $this->property1 = $property1;
            
                    return $this;
                }

                public function setProperty2(string ...$property2): self
                {
                    $this->property2 = $property2;
            
                    return $this;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testToStringWhenResponseGiven_shouldGenerateResponseClass()
    {
        $properties = [
            $this->getPropertyTemplate('property1', PropertyType::int(), false, 'First'),
        ];
        $sut        = $this->getSut(true, ...$properties);

        $result         = (string)$sut;
        $expectedResult = <<<'EXPECTED'
            <?php
            declare(strict_types=1);
            
            namespace Root\Model;
            
            class Model extends ModelAbstract implements ResponseInterface
            {
                use ResponseTrait;
                
                /**
                 * @var int First
                 */
                protected int $property1;

                public function __construct(int $property1)
                {
                    $this->property1 = $property1;
                }

                public function getProperty1(): int
                {
                    return $this->property1;
                }
            
                public function setProperty1(int $property1): self
                {
                    $this->property1 = $property1;
            
                    return $this;
                }
            }
            EXPECTED;

        $this->assertRenderedStringSame($expectedResult, $result);
    }

    public function testGetDirectory()
    {
        $directory = $this->getSut(false)->getDirectory();

        $this->assertSame('/src/Model/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut(false)->getClassName(true);

        $this->assertSame('Root\Model\Model', $className);
    }

    private function getPropertyTemplate(string $name, PropertyType $type, bool $isNullable, ?string $description): ModelPropertyTemplate
    {
        return new ModelPropertyTemplate(
            $this->typeMapper,
            $this->stringHelper,
            $name,
            $type,
            true,
            $isNullable,
            $description
        );
    }

    private function getSut(bool $isResponse, ModelPropertyTemplate ...$properties): ModelTemplate
    {
        return new ModelTemplate($this->locationHelper, $this->stringHelper, $this->typeMapper, 'Model', $isResponse, ...$properties);
    }
}
