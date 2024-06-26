<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use DI\Container;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Mapper\TypeMapper;

class Factory
{
    public function __construct(private readonly Container $diContainer)
    {
    }

    public function getModelAbstractTemplate(): ModelAbstractTemplate
    {
        return new ModelAbstractTemplate(
            $this->diContainer->get(LocationHelper::class),
        );
    }

    public function getModelTemplate(string $modelName, bool $isResponse, ModelPropertyTemplate ...$properties): ModelTemplate
    {
        return new ModelTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $this->diContainer->get(TypeMapper::class),
            $modelName,
            $isResponse,
            ...$properties
        );
    }

    public function getModelPropertyTemplate(
        string $name,
        PropertyType $type,
        bool $isRequired,
        bool $isNullable,
        ?string $description = null
    ): ModelPropertyTemplate {
        return new ModelPropertyTemplate(
            $this->diContainer->get(TypeMapper::class),
            $this->diContainer->get(StringHelper::class),
            $name,
            $type,
            $isRequired,
            $isNullable,
            $description
        );
    }

    public function getEnumTemplate(string $name, string ...$values): EnumTemplate
    {
        return new EnumTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $name,
            ...$values
        );
    }

    public function getGeneralResponseTemplate(): GeneralResponseTemplate
    {
        return new GeneralResponseTemplate(
            $this->diContainer->get(LocationHelper::class),
        );
    }

    public function getResponseInterfaceTemplate(): ResponseInterfaceTemplate
    {
        return new ResponseInterfaceTemplate(
            $this->diContainer->get(LocationHelper::class),
        );
    }

    public function getResponseListInterfaceTemplate(): ResponseListInterfaceTemplate
    {
        return new ResponseListInterfaceTemplate(
            $this->diContainer->get(LocationHelper::class),
        );
    }

    public function getResponseListTemplate(string $itemClassName): ResponseListTemplate
    {
        return new ResponseListTemplate(
            $this->diContainer->get(LocationHelper::class),
            $itemClassName
        );
    }

    public function getResponseTraitTemplate(): ResponseTraitTemplate
    {
        return new ResponseTraitTemplate(
            $this->diContainer->get(LocationHelper::class),
        );
    }
}
