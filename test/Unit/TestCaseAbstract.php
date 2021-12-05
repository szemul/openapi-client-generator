<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class TestCaseAbstract extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function assertRenderedStringSame(string $expected, string $actual)
    {
        $whiteSpaceRemovePattern = '#[^\S\r\n]{4}#';
        $emptyLineRemovePattern  = '#(\R){2,}#';

        $expected = preg_replace($whiteSpaceRemovePattern, '', trim($expected));
        $expected = preg_replace($emptyLineRemovePattern, '$1', $expected);
        $actual   = preg_replace($whiteSpaceRemovePattern, '', trim($actual));
        $actual   = preg_replace($emptyLineRemovePattern, '$1', $actual);

        $this->assertSame($expected, $actual);
    }
}
