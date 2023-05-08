<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

use Emul\OpenApiClientGenerator\Entity\ExceptionClass;
use Emul\OpenApiClientGenerator\Entity\ResponseClass;
use Exception;

class SchemaHelper
{
    public function __construct(private readonly ClassHelper $classHelper)
    {
    }

    /**
     * @return ResponseClass[]
     */
    public function getActionResponseClasses(array $actionDetails): array
    {
        $responseClasses = [];

        foreach ($actionDetails['responses'] as $statusCode => $response) {
            $statusCode = (int)$statusCode;
            if ($statusCode >= 300) {
                continue;
            }

            $responseIsList = false;
            $responseClass  = 'GeneralResponse';

            if (!empty($response['content']['application/json']['schema'])) {
                $schema = $response['content']['application/json']['schema'];

                if (!empty($schema['$ref'])) {
                    $responseClass = $this->classHelper->getModelClassname($schema['$ref']);
                } elseif ($schema['type'] === 'array') {
                    $responseIsList = true;
                    $responseClass  = $this->classHelper->getListModelClassname(basename($schema['items']['$ref']));
                }
            }

            $responseClasses[] = new ResponseClass($statusCode, $responseIsList, $responseClass);
        }

        return $responseClasses;
    }

    /**
     * @return ExceptionClass[]
     */
    public function getActionExceptionClasses(array $actionDetails): array
    {
        /** @var ExceptionClass[] $exceptionClasses */
        $exceptionClasses = [];

        foreach ($actionDetails['responses'] as $statusCode => $response) {
            $statusCode = (int)$statusCode;
            if ($statusCode < 400) {
                continue;
            }

            $className          = $this->classHelper->getRequestExceptionClassName($statusCode);
            $description        = $response['description'] ?? '';
            $exceptionClasses[] = new ExceptionClass($statusCode, $description, $className);
        }

        return $exceptionClasses;
    }

    public function uniteAllOfSchema(array $schemas, string $allOfSchemaName): array
    {
        return [
            'properties' => $this->unfoldAndUniteAllOfSchema($schemas, $allOfSchemaName),
        ];
    }

    /**
     * @return string[]
     */
    public function getResponseListClassNames(array $paths): array
    {
        $responseListClassNames = [];
        foreach ($paths as $methods) {
            foreach ($methods as $details) {
                foreach ($details['responses'] as $statusCode => $response) {
                    if ($statusCode >= 300 || empty($response['content']['application/json']['schema']['type'])) {
                        continue;
                    }

                    $schema = $response['content']['application/json']['schema'];
                    if ($schema['type'] === 'array') {
                        $responseListClassNames[] = $this->classHelper->getModelClassname(basename($schema['items']['$ref']));
                    }
                }
            }
        }

        return $responseListClassNames;
    }

    /**
     * @return string[]
     */
    public function getResponseSchemaNames(array $paths): array
    {
        $schemaNames = [];
        foreach ($paths as $methods) {
            foreach ($methods as $details) {
                foreach ($details['responses'] as $statusCode => $response) {
                    if ($statusCode >= 300 || empty($response['content']['application/json']['schema'])) {
                        continue;
                    }

                    $schema = $response['content']['application/json']['schema'];

                    if (!empty($schema['$ref'])) {
                        $schemaNames[] = basename($schema['$ref']);
                    }
                }
            }
        }

        return $schemaNames;
    }

    private function unfoldAndUniteAllOfSchema(array $schemas, string $allOfSchemaName)
    {
        $unitedSchema = [];
        foreach ($schemas[$allOfSchemaName]['allOf'] as $definition) {
            if (!empty($definition['properties'])) {
                $unitedSchema = array_merge($unitedSchema, $definition['properties']);
            } elseif (!empty($definition['$ref'])) {
                $referredSchemaName = '';
                $referredSchema     = $this->getReferredSchema($schemas, $definition['$ref'], $referredSchemaName);
                $unitedSchema       = array_merge($unitedSchema, $this->unfoldSchema($schemas, $referredSchema, $referredSchemaName));
            } else {
                throw new Exception('Unhandled allOf part');
            }
        }

        return $unitedSchema;
    }

    private function getReferredSchema(array $schemas, string $referencePath, string &$referenceName): array
    {
        $referenceName = str_replace('#/components/schemas/', '', $referencePath);

        if (empty($schemas[$referenceName])) {
            throw new Exception('Referred schema does not exist: ' . $referenceName);
        }

        return $schemas[$referenceName];
    }

    private function unfoldSchema(array $schemas, array $referredSchema, string $referredSchemaName): array
    {
        if (!empty($referredSchema['allOf'])) {
            return $this->unfoldAndUniteAllOfSchema($schemas, $referredSchemaName);
        } elseif (!empty($referredSchema['properties'])) {
            return $referredSchema['properties'];
        } else {
            throw new Exception('Unhandled schema definition in ' . $referredSchemaName);
        }
    }
}
