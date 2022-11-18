<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;
use Emul\OpenApiClientGenerator\Template\Model\ModelPropertyTemplate;
use Emul\OpenApiClientGenerator\Template\Model\ModelTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ModelTemplateTest extends TemplateTestCaseAbstract
{
    public function testToString_shouldGenerateClass()
    {
        $properties = [
            $this->getPropertyTemplate('property1', PropertyType::int(), true, 'First'),
            $this->getPropertyTemplate('property2', PropertyType::array(PropertyType::string()), false, 'Second'),
        ];
        $sut        = $this->getSut(...$properties);

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

    public function testGetDirectory()
    {
        $directory = $this->getSut()->getDirectory();

        $this->assertSame('/src/Model/', $directory);
    }

    public function testGetClassname()
    {
        $className = $this->getSut()->getClassName(true);

        $this->assertSame('Root\Model\Model', $className);
    }

    private function getPropertyTemplate(string $name, PropertyType $type, bool $isRequired, ?string $description): ModelPropertyTemplate
    {
        return new ModelPropertyTemplate(
            $this->locationHelper,
            $this->stringHelper,
            $this->typeMapper,
            $name,
            $type,
            $isRequired,
            $description
        );
    }

    private function getSut(ModelPropertyTemplate ...$properties): ModelTemplate
    {
        return new ModelTemplate($this->locationHelper, $this->stringHelper, $this->typeMapper, 'Model', ...$properties);
    }
}
