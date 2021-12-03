<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Exception;

use Emul\OpenApiClientGenerator\Entity\PropertyType;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

class Factory
{
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    public function __construct(LocationHelper $locationHelper, StringHelper $stringHelper)
    {
        $this->locationHelper = $locationHelper;
        $this->stringHelper   = $stringHelper;
    }

    public function getPropertyNotInitializedExceptionTemplate(): PropertyNotInitializedExceptionTemplate
    {
        return new PropertyNotInitializedExceptionTemplate($this->locationHelper, $this->stringHelper);
    }

    public function getRequestCodeExceptionTemplate(
        int $errorCode,
        RequestExceptionPropertyTemplate ...$properties
    ): RequestCodeExceptionTemplate {
        return new RequestCodeExceptionTemplate($this->locationHelper, $this->stringHelper, $errorCode, ...$properties);
    }

    public function getRequestExceptionPropertyTemplate(
        string $name,
        PropertyType $type,
        ?string $description = null
    ): RequestExceptionPropertyTemplate {
        return new RequestExceptionPropertyTemplate($this->locationHelper, $this->stringHelper, $name, $type, $description);
    }

    public function getRequestExceptionTemplate(): RequestExceptionTemplate
    {
        return new RequestExceptionTemplate($this->locationHelper, $this->stringHelper);
    }
}
