<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class ArrayMapperFactoryTemplate extends ClassTemplateAbstract
{
    /** @var string[] */
    private array $entityClasses = [];

    public function __construct(LocationHelper $locationHelper, StringHelper $stringHelper, string ...$entityClasses)
    {
        parent::__construct($locationHelper, $stringHelper);

        $this->entityClasses = $entityClasses;
    }

    public function __toString(): string
    {
        return <<<FACTORY
            <?php
            
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Carbon\\Carbon;
            use Carbon\\CarbonInterface;
            use Closure;
            use Emul\\ArrayToClassMapper\\MapperFactory;
            use Emul\\ArrayToClassMapper\\Mapper;
            
            class {$this->getClassName()}
            {
                private Mapper \$mapper;
                private array \$entityClasses = [{$this->getEnumClasses()}];
            
                public function __construct()
                {
                    \$this->mapper = (new MapperFactory())->getMapper();
                    \$this->addCustomMappers();
                }
            
                public function getMapper(): Mapper
                {
                    return \$this->mapper;
                }
            
                private function addCustomMappers(): void
                {
                    \$this->addCarbonMapper();
                    \$this->addEntityMappers();
                }
            
                private function addCarbonMapper(): void
                {
                    \$carbonMapper = Closure::fromCallable(
                        function (?string \$timeString) {
                            return empty(\$timeString)
                                ? null
                                : Carbon::createFromFormat(CarbonInterface::ATOM, \$timeString);
                        }
                    );
            
                    \$this->mapper->addCustomMapper(CarbonInterface::class, \$carbonMapper);
                }
            
                private function addEntityMappers(): void
                {
                    foreach (\$this->entityClasses as \$entityClass) {
                        \$this->mapper->addCustomMapper(\$entityClass, \$this->enumConverter(\$entityClass));
                    }
                }
            
                private function enumConverter(string \$enumClass): Closure
                {
                    return Closure::fromCallable(
                        fn (?string \$method) => empty(\$method)
                            ? null
                            : \$enumClass::createFromString(\$method)
                    );
                }
            }
            FACTORY;
    }

    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getSrcPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getRootNamespace();
    }

    protected function getShortClassName(): string
    {
        return 'ArrayMapperFactory';
    }

    private function getEnumClasses(): string
    {
        $result = [];
        foreach ($this->entityClasses as $entityClass) {
            $result[] = "'" . addslashes($entityClass) . "'";
        }

        return implode(', ', $result);
    }
}
