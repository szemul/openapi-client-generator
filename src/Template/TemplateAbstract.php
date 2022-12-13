<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

abstract class TemplateAbstract
{
    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper
    ) {
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
