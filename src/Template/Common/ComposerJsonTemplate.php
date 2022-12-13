<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ComposerJsonTemplate extends TemplateAbstract
{

    public function __construct(
        LocationHelper          $locationHelper,
        StringHelper            $stringHelper,
        private readonly string $vendorName,
        private readonly string $projectName,
        private readonly string $description
    ) {
        parent::__construct($locationHelper, $stringHelper);
    }

    public function __toString(): string
    {
        return <<<COMPOSER
            {
                "name": "{$this->vendorName}/$this->projectName",
                "description": "{$this->description}",
                "minimum-stability": "stable",
                "license": "MIT",
                "require": {
                    "php": ">=7.4",
                    "ext-json": "*",
                    "nesbot/carbon": "^2.0",
                    "emulgeator/enum": "^1.0",
                    "emulgeator/array-to-class-mapper": "^0.1",
                    "psr/http-message": "^1.0",
                    "psr/http-client": "^1.0",
                    "psr/http-factory": "^1.0"
                },
                "autoload": {
                  "psr-4": {
                    "{$this->getLocationHelper()->getEscapedRootNamespace()}\\\\": "src"
                  }
                }
            }
            COMPOSER;
    }
}
