<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Template\Common\ArrayMapperFactoryTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ArrayMapperFactoryTemplateTest extends TemplateTestCaseAbstract
{
    private array $entityClasses = ['\\Root\\Entity\\First', '\\Root\\Entity\\Second'];

    public function testToString_shouldRenderProperly()
    {
        $sut = $this->getSut();

        $result = (string)$sut;

        $expectedResult = <<<'EXPECTED'
            <?php
            
            declare(strict_types=1);
            
            namespace Root;
            
            use Carbon\Carbon;
            use Carbon\CarbonInterface;
            use Closure;
            use Emul\ArrayToClassMapper\MapperFactory;
            use Emul\ArrayToClassMapper\Mapper;
            
            class ArrayMapperFactory
            {
                private Mapper $mapper;
                private array $entityClasses = ['\\Root\\Entity\\First', '\\Root\\Entity\\Second'];
            
                public function __construct()
                {
                    $this->mapper = (new MapperFactory())->getMapper();
                    $this->addCustomMappers();
                }
            
                public function getMapper(): Mapper
                {
                    return $this->mapper;
                }
            
                private function addCustomMappers(): void
                {
                    $this->addCarbonMapper();
                    $this->addEntityMappers();
                }
            
                private function addCarbonMapper(): void
                {
                    $carbonMapper = Closure::fromCallable(
                        function (?string $timeString) {
                            return empty($timeString)
                                ? null
                                : Carbon::createFromFormat(CarbonInterface::ATOM, $timeString);
                        }
                    );
            
                    $this->mapper->addCustomMapper(CarbonInterface::class, $carbonMapper);
                }
            
                private function addEntityMappers(): void
                {
                    foreach ($this->entityClasses as $entityClass) {
                        $this->mapper->addCustomMapper($entityClass, $this->enumConverter($entityClass));
                    }
                }
            
                private function enumConverter(string $enumClass): Closure
                {
                    return Closure::fromCallable(
                        fn (?string $method) => empty($method)
                            ? null
                            : $enumClass::createFromString($method)
                    );
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

        $this->assertSame('Root\ArrayMapperFactory', $className);
    }

    private function getSut(): ArrayMapperFactoryTemplate
    {
        return new ArrayMapperFactoryTemplate($this->locationHelper, $this->stringHelper, ...$this->entityClasses);
    }
}
