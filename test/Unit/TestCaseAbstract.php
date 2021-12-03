<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class TestCaseAbstract extends TestCase
{
    use MockeryPHPUnitIntegration;
}
