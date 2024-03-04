<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Mapper;

use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterType;

class ParameterMapper
{
    public function __construct(private readonly TypeMapper $typeMapper)
    {
    }

    public function mapParameter(string $operationId, array $parameterDetails): Parameter
    {
        $name        = $parameterDetails['name'];
        $type        = ParameterType::createFromString($parameterDetails['in']);
        $isRequired  = empty($parameterDetails['required']) ? false : $parameterDetails['required'];
        $valueType   = $this->typeMapper->mapParameterToPropertyType($operationId, $parameterDetails);
        $description = $parameterDetails['description'] ?? null;

        return new Parameter($name, $type, $isRequired, $valueType, $description);
    }
}
