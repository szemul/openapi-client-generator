<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Mapper;

use Emul\OpenApiClientGenerator\Entity\Parameter;
use Emul\OpenApiClientGenerator\Entity\ParameterType;

class ParameterMapper
{
    private TypeMapper $typeMapper;

    public function __construct(TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    public function mapParameter(array $parameterDetails): Parameter
    {
        $name        = $parameterDetails['name'];
        $type        = ParameterType::createFromString($parameterDetails['in']);
        $isRequired  = empty($parameterDetails['required']) ? false : $parameterDetails['required'];
        $valueType   = $this->typeMapper->mapParameterToPropertyType($parameterDetails);
        $description = $parameterDetails['description'] ?? null;

        return new Parameter($name, $type, $isRequired, $valueType, $description);
    }
}
