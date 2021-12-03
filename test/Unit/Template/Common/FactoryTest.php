<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Common;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Common\ArrayMapperFactoryTemplate;
use Emul\OpenApiClientGenerator\Template\Common\ComposerJsonTemplate;
use Emul\OpenApiClientGenerator\Template\Common\ConfigurationTemplate;
use Emul\OpenApiClientGenerator\Template\Common\Factory;
use Emul\OpenApiClientGenerator\Template\Common\JsonSerializableTraitTemplate;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Mockery;

class FactoryTest extends TestCaseAbstract
{
    private LocationHelper $locationHelper;
    private StringHelper   $stringHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locationHelper = Mockery::mock(LocationHelper::class);
        $this->stringHelper   = Mockery::mock(StringHelper::class);
    }

    public function testGetArrayMapperFactoryTemplate()
    {
        $result = $this->getSut()->getArrayMapperFactoryTemplate();

        $this->assertInstanceOf(ArrayMapperFactoryTemplate::class, $result);
    }

    public function testGetComposerJsonTemplate()
    {
        $result = $this->getSut()->getComposerJsonTemplate('vendor', 'project', 'description');

        $this->assertInstanceOf(ComposerJsonTemplate::class, $result);
    }

    public function testGetConfigurationTemplate()
    {
        $result = $this->getSut()->getConfigurationTemplate();

        $this->assertInstanceOf(ConfigurationTemplate::class, $result);
    }

    public function testGetTJsonSerializableTemplate()
    {
        $result = $this->getSut()->getTJsonSerializableTemplate();

        $this->assertInstanceOf(JsonSerializableTraitTemplate::class, $result);
    }

    private function getSut(): Factory
    {
        return new Factory($this->locationHelper, $this->stringHelper);
    }
}
