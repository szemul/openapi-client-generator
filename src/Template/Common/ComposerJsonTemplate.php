<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Common;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\TemplateAbstract;

class ComposerJsonTemplate extends TemplateAbstract
{
    private string $vendorName;
    private string $projectName;
    private string $description;

    public function __construct(
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $vendorName,
        string $projectName,
        string $description
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->vendorName  = $vendorName;
        $this->projectName = $projectName;
        $this->description = $description;
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
                    "psr/http-message-implementation": "^1.0",
                    "psr/http-client": "^1.0",
                    "psr/http-client-implementation": "^1.0",
                    "psr/http-factory": "^1.0",
                    "psr/http-factory-implementation": "^1.0"
                },
                "require-dev": {
                    "guzzlehttp/guzzle": "^7.4",
                    "guzzlehttp/psr7": "^2.1.0"
                },
                "autoload": {
                  "psr-4": {
                    "{$this->getLocationHelper()->getRootNamespace()}\\\\": "src"
                  }
                }
            }
            COMPOSER;
    }
}
