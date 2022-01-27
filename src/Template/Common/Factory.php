<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use DI\Container;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;

class Factory
{
    private Container $diContainer;

    public function __construct(Container $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function getArrayMapperFactoryTemplate(string ...$entityClasses): ArrayMapperFactoryTemplate
    {
        return new ArrayMapperFactoryTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            ...$entityClasses
        );
    }

    public function getComposerJsonTemplate(string $vendorName, string $projectName, string $description): ComposerJsonTemplate
    {
        return new ComposerJsonTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class),
            $vendorName,
            $projectName,
            $description
        );
    }

    public function getConfigurationTemplate(): ConfigurationTemplate
    {
        return new ConfigurationTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class)
        );
    }

    public function getJsonSerializableTraitTemplate(): JsonSerializableTraitTemplate
    {
        return new JsonSerializableTraitTemplate(
            $this->diContainer->get(LocationHelper::class),
            $this->diContainer->get(StringHelper::class)
        );
    }
}
