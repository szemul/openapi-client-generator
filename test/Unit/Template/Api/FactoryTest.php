<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Template\Api;

use Emul\OpenApiClientGenerator\Entity\HttpMethod;
use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\Api\ApiActionTemplate;
use Emul\OpenApiClientGenerator\Template\Api\ApiTemplate;
use Emul\OpenApiClientGenerator\Template\Api\Factory;
use Emul\OpenApiClientGenerator\Template\Model\ActionParameterTemplate;
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
        $this->stringHelper   = new StringHelper();
    }

    public function testGetApiTemplate()
    {
        $template = $this->getSut()->getApiTemplate('tag');

        $this->assertInstanceOf(ApiTemplate::class, $template);
    }

    public function testGetApiActionTemplate()
    {
        $template = $this->getSut()->getApiActionTemplate('operation', 'Action', 'url', HttpMethod::post(), null, null);

        $this->assertInstanceOf(ApiActionTemplate::class, $template);
    }

    public function testGetActionParameterTemplate()
    {
        $template = $this->getSut()->getActionParameterTemplate('Class', null);

        $this->assertInstanceOf(ActionParameterTemplate::class, $template);
    }

    private function getSut(): Factory
    {
        return new Factory($this->locationHelper, $this->stringHelper);
    }
}
