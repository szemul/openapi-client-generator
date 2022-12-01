<?php

declare(strict_types=1);

namespace Test;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Emul\ArrayToClassMapper\MapperFactory;
use Emul\ArrayToClassMapper\Mapper;

class ArrayMapperFactory
{
    private Mapper $mapper;
    private array $entityClasses = ['Test\\Model\\Enum\\OrderUpsertEventAction'];

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
                if (empty($timeString)) {
                    return null;
                }

                try {
                    return Carbon::createFromFormat(CarbonInterface::ATOM, $timeString);
                } catch (InvalidFormatException $e) {
                    return Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $timeString);
                }
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
