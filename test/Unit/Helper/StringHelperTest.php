<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Helper;

use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;

class StringHelperTest extends TestCaseAbstract
{
    public static function constantProvider(): array
    {
        return [
            ['0', 'VALUE_0'],
            ['word', 'WORD'],
            ['with spaces', 'WITH_SPACES'],
            ['special& char', 'SPECIAL_CHAR'],
            ['camelCase', 'CAMEL_CASE'],
            ['30days', 'DAYS_30'],
            ['dash-case', 'DASH_CASE'],
            ['30_days', 'DAYS_30'],
            ['_days', 'DAYS'],
            ['UPPER', 'UPPER'],
        ];
    }

    /**
     * @dataProvider constantProvider
     */
    public function testConvertToConstantName(string $input, $expectedResult)
    {
        $result = $this->getSut()->convertToConstantName($input);

        $this->assertSame($expectedResult, $result);
    }

    public static function methodProvider(): array
    {
        return [
            ['0', 'value0'],
            ['word', 'word'],
            ['with spaces in it', 'withSpacesInIt'],
            ['special& char', 'specialChar'],
            ['simpleCamelCase', 'simpleCamelCase'],
            ['UpperCase', 'upperCase'],
            ['dash-case', 'dashCase'],
            ['30_days', 'days30'],
        ];
    }

    /**
     * @dataProvider methodProvider
     */
    public function testConvertToMethodOrVariableName($input, $expectedResult)
    {
        $result = $this->getSut()->convertToMethodOrVariableName($input);

        $this->assertSame($expectedResult, $result);
    }

    public static function classProvider(): array
    {
        return [
            ['0', 'Value0'],
            ['word', 'Word'],
            ['with spaces in it', 'WithSpacesInIt'],
            ['special& char', 'SpecialChar'],
            ['simpleCamelCase', 'SimpleCamelCase'],
            ['30_days', 'Days30'],
            ['dash-case', 'DashCase'],
            ['internal.apiGateway', 'InternalApiGateway'],
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
