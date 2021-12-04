<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Helper;

use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class StringHelperTest extends TestCaseAbstract
{
    public function constantProvider(): array
    {
        return [
            ['word', 'WORD'],
            ['with spaces', 'WITH_SPACES'],
            ['special& char', 'SPECIAL_CHAR'],
            ['camelCase', 'CAMEL_CASE'],
            ['30days', 'DAYS_30'],
            ['30_days', 'DAYS_30'],
        ];
    }

    /**
     * @dataProvider constantProvider
     */
    public function testConvertToConstantName($input, $expectedResult)
    {
        $result = $this->getSut()->convertToConstantName($input);

        $this->assertSame($expectedResult, $result);
    }

    public function methodProvider(): array
    {
        return [
            ['word', 'word'],
            ['with spaces in it', 'withSpacesInIt'],
            ['special& char', 'specialChar'],
            ['simpleCamelCase', 'simpleCamelCase'],
            ['30_days', 'days30']
        ];
    }

    /**
     * @dataProvider methodProvider
     */
    public function testConvertToMethodName($input, $expectedResult)
    {
        $result = $this->getSut()->convertToMethodOrVariableName($input);

        $this->assertSame($expectedResult, $result);
    }

    public function classProvider(): array
    {
        return [
            ['word', 'Word'],
            ['with spaces in it', 'WithSpacesInIt'],
            ['special& char', 'SpecialChar'],
            ['simpleCamelCase', 'SimpleCamelCase'],
            ['30_days', 'Days30']
        ];
    }

    /**
     * @dataProvider classProvider
     */
    public function testConvertToClassName($input, $expectedResult)
    {
        $result = $this->getSut()->convertToClassName($input);

        $this->assertSame($expectedResult, $result);
    }

    private function getSut(): StringHelper
    {
        return new StringHelper();
    }
}
