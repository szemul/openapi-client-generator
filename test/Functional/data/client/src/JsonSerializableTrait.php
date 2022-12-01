<?php
declare(strict_types=1);

namespace Test;

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
