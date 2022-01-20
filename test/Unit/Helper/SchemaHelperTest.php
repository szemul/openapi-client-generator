<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Helper;

use Emul\OpenApiClientGenerator\Helper\SchemaHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Exception;

class SchemaHelperTest extends TestCaseAbstract
{
    public function testUniteAllOfSchemaWhenJustPropertiesGiven_shouldMergeThem()
    {
        $sut = $this->getSut();

        $schemas = [
            'name' => [
                'allOf' => [
                    ['properties' => ['first' => 1]],
                    ['properties' => ['second' => 2]],
                ],
            ],
        ];

        $result         = $sut->uniteAllOfSchema($schemas, 'name');
        $expectedResult = [
            'properties' => [
                'first'  => 1,
                'second' => 2,
            ],
        ];

        $this->assertSame($expectedResult, $result);
    }

    public function testUniteAllOfSchemaWhenRefersToAnotherSchema_shouldUnfoldAndMergeThem()
    {
        $sut = $this->getSut();

        $schemas = [
            'name' => [
                'allOf' => [
                    ['properties' => ['first' => 1]],
                    ['$ref' => '#/components/schemas/ref'],
                ],
            ],
            'ref'  => [
                'properties' => ['second' => 2],
            ],
        ];

        $result         = $sut->uniteAllOfSchema($schemas, 'name');
        $expectedResult = [
            'properties' => [
                'first'  => 1,
                'second' => 2,
            ],
        ];

        $this->assertSame($expectedResult, $result);
    }

    public function testUniteAllOfSchemaWhenRefersToAnotherSchemaWhichIsAlsoAllOf_shouldUnfoldAndMergeThem()
    {
        $sut = $this->getSut();

        $schemas = [
            'name' => [
                'allOf' => [
                    ['properties' => ['first' => 1]],
                    ['$ref' => '#/components/schemas/ref'],
                ],
            ],
            'ref'  => [
                'allOf' => [
                    ['properties' => ['second' => 2]],
                    ['properties' => ['third' => 3]],
                ],
            ],
        ];

        $result         = $sut->uniteAllOfSchema($schemas, 'name');
        $expectedResult = [
            'properties' => [
                'first'  => 1,
                'second' => 2,
                'third'  => 3,
            ],
        ];

        $this->assertSame($expectedResult, $result);
    }

    public function testUniteAllOfSchemaWhenContainsUnknownKey_shouldThrowException()
    {
        $sut = $this->getSut();

        $schemas = [
            'name' => [
                'allOf' => [
                    ['unknown' => 1],
                ],
            ],
        ];

        $this->expectException(Exception::class);
        $sut->uniteAllOfSchema($schemas, 'name');
    }

    public function testUniteAllOfSchemaWhenRefersToAnotherSchemaWhatDoesNotExist_shouldThrowException()
    {
        $sut = $this->getSut();

        $schemas = [
            'name' => [
                'allOf' => [
                    ['properties' => ['first' => 1]],
                    ['$ref' => '#/components/schemas/ref'],
                ],
            ],
        ];

        $this->expectException(Exception::class);
        $sut->uniteAllOfSchema($schemas, 'name');
    }

    public function getSut(): SchemaHelper
    {
        return new SchemaHelper();
    }
}
