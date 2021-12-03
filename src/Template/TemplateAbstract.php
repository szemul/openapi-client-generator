<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

abstract class TemplateAbstract
{
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    public function __construct(LocationHelper $locationHelper, StringHelper $stringHelper)
    {
        $this->locationHelper = $locationHelper;
        $this->stringHelper   = $stringHelper;
    }

    protected function getLocationHelper(): LocationHelper
    {
        return $this->locationHelper;
    }

    protected function getStringHelper(): StringHelper
    {
        return $this->stringHelper;
    }
}
