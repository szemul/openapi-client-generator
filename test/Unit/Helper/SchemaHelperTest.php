<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Test\Unit\Helper;

use Emul\OpenApiClientGenerator\Helper\ClassHelper;
use Emul\OpenApiClientGenerator\Helper\SchemaHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Test\Unit\TestCaseAbstract;
use Exception;

class SchemaHelperTest extends TestCaseAbstract
{
    public function testGetActionResponseClasses()
    {
        $actionDetails = [
            'responses' => [
                200 => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type'  => 'array',
                                'items' => [
                                    '$ref' => '#/components/schemas/Item',
                                ],
                            ],
                        ],
                    ],
                ],
                201 => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Individual',
                            ],
                        ],
                    ],
                ],
                204 => [],
            ],
        ];

        $responseClasses = $this->getSut()->getActionResponseClasses($actionDetails);

        $this->assertCount(3, $responseClasses);
        $this->assertSame(200, $responseClasses[0]->getStatusCode());
        $this->assertSame('ItemList', $responseClasses[0]->getModelClassName());
        $this->assertTrue($responseClasses[0]->isList());
        $this->assertSame(201, $responseClasses[1]->getStatusCode());
        $this->assertSame('Individual', $responseClasses[1]->getModelClassName());
        $this->assertFalse($responseClasses[1]->isList());
        $this->assertSame(204, $responseClasses[2]->getStatusCode());
        $this->assertSame('GeneralResponse', $responseClasses[2]->getModelClassName());
        $this->assertFalse($responseClasses[2]->isList());
    }

    public function testGetResponseSchemaNames()
    {
        $paths = [
            '/order' => [
                'get'  => [
                    'responses' => [
                        200 => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/GetResponse',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'post' => [
                    'responses' => [
                        200 => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/PostResponse',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseSchemaNames = $this->getSut()->getResponseSchemaNames($paths);

        $this->assertCount(2, $responseSchemaNames);
        $this->assertSame('GetResponse', $responseSchemaNames[0]);
        $this->assertSame('PostResponse', $responseSchemaNames[1]);
    }

    public function testGetResponseListClassNames()
    {
        $paths = [
            '/order' => [
                'get'  => [
                    'responses' => [
                        200 => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type'  => 'array',
                                        'items' => [
                                            '$ref' => '#/components/schemas/GetResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'post' => [
                    'responses' => [
                        200 => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type'  => 'array',
                                        'items' => [
                                            '$ref' => '#/components/schemas/PostResponse',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseSchemaNames = $this->getSut()->getResponseListClassNames($paths);

        $this->assertCount(2, $responseSchemaNames);
        $this->assertSame('GetResponse', $responseSchemaNames[0]);
        $this->assertSame('PostResponse', $responseSchemaNames[1]);
    }

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

    public function testGetReferencedValue_shouldReturnProperValue()
    {
        $reference                = '#/components/parameters/parameterName';
        $expectedResult           = [
            'required' => true,
            'type'     => 'string',
        ];
        $documentation = [
            'components' => [
                'parameters' => [
                    'parameterName' => $expectedResult,
                ],
            ],
            'somethingElse' => 'other',
        ];

        $result = $this->getSut()->getReferencedValue($reference, $documentation);

        $this->assertSame($expectedResult, $result);
    }

    public function getSut(): SchemaHelper
    {
        return new SchemaHelper(new ClassHelper(new StringHelper()));
    }
}
