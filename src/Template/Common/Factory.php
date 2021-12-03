<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

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

    public function getArrayMapperFactoryTemplate(string ...$entityClasses): ArrayMapperFactoryTemplate
    {
        return new ArrayMapperFactoryTemplate($this->locationHelper, $this->stringHelper, ...$entityClasses);
    }

    public function getComposerJsonTemplate(string $vendorName, string $projectName, string $description): ComposerJsonTemplate
    {
        return new ComposerJsonTemplate($this->locationHelper, $this->stringHelper, $vendorName, $projectName, $description);
    }

    public function getConfigurationTemplate(): ConfigurationTemplate
    {
        return new ConfigurationTemplate($this->locationHelper, $this->stringHelper);
    }

    public function getTJsonSerializableTemplate(): JsonSerializableTraitTemplate
    {
        return new JsonSerializableTraitTemplate($this->locationHelper, $this->stringHelper);
    }
}
