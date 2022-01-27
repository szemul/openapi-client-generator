<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use DI\Container;
use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

class Factory
{
    private Container $diContainer;

    public function __construct(Container $container)
    {
        $this->diContainer = $container;
    }

    public function getPropertyNotInitializedExceptionTemplate(): PropertyNotInitializedExceptionTemplate
    {
        return new PropertyNotInitializedExceptionTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
        );
    }

    public function getRequestCodeExceptionTemplate(
        int $errorCode,
        RequestExceptionPropertyTemplate ...$properties
    ): RequestCodeExceptionTemplate {
        return new RequestCodeExceptionTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $errorCode,
            ...$properties
        );
    }

    public function getRequestExceptionPropertyTemplate(
        string $name,
        PropertyType $type,
        ?string $description = null
    ): RequestExceptionPropertyTemplate {
        return new RequestExceptionPropertyTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $name,
            $type,
            $description
        );
    }

    public function getRequestExceptionTemplate(): RequestExceptionTemplate
    {
        return new RequestExceptionTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
        );
    }
}
