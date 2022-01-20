<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

use Exception;

class SchemaHelper
{
    public function uniteAllOfSchema(array $schemas, string $allOfSchemaName): array
    {
        return [
            'properties' => $this->unfoldAndUniteAllOfSchema($schemas, $allOfSchemaName),
        ];
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
