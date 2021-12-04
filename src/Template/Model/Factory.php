<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;

class Factory
{
    private TypeMapper     $typeMapper;
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    public function __construct(TypeMapper $typeMapper, LocationHelper $locationHelper, StringHelper $stringHelper)
    {
        $this->typeMapper     = $typeMapper;
        $this->locationHelper = $locationHelper;
        $this->stringHelper   = $stringHelper;
    }

    public function getModelAbstractTemplate(): ModelAbstractTemplate
    {
        return new ModelAbstractTemplate($this->locationHelper, $this->stringHelper);
    }

    public function getModelTemplate(string $modelName, ModelPropertyTemplate ...$properties): ModelTemplate
    {
        return new ModelTemplate($this->locationHelper, $this->stringHelper, $this->typeMapper, $modelName, ...$properties);
    }

    public function getModelPropertyTemplate(
        string $name,
        PropertyType $type,
        bool $isRequired,
        ?string $description = null
    ): ModelPropertyTemplate {
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

    public function getEnumTemplate(string $name, string $namespace, string ...$values): EnumTemplate
    {
        return new EnumTemplate($this->locationHelper, $this->stringHelper, $name, $namespace, ...$values);
    }
}
