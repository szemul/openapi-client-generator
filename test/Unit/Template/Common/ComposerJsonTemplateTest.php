<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Template\Common\ComposerJsonTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\Template\TemplateTestCaseAbstract;

class ComposerJsonTemplateTest extends TemplateTestCaseAbstract
{
    private string $vendorName  = 'vendor';
    private string $projectName = 'project';
    private string $description = 'description';

    public function testToString()
    {
        $result = (string)$this->getSut();

        $expectedResult = <<<'EXPECTED'
            {
                "name": "vendor/project",
                "description": "description",
                "minimum-stability": "stable",
                "license": "MIT",
                "require": {
                    "php": ">=8.0",
                    "ext-json": "*",
                    "nesbot/carbon": "^2.0",
                    "emulgeator/enum": "^1.0",
                    "emulgeator/array-to-class-mapper": "^0.1|^1.0",
                    "psr/http-message": "^1.0|^2.0",
                    "psr/http-client": "^1.0",
                    "psr/http-factory": "^1.0"
                },
                "autoload": {
                  "psr-4": {
                    "Root\\": "src"
                  }
                }
            }
            EXPECTED;

        $this->assertSame($expectedResult, $result);
    }

    private function getSut(): ComposerJsonTemplate
    {
        return new ComposerJsonTemplate($this->locationHelper, $this->vendorName, $this->projectName, $this->description);
    }
}
