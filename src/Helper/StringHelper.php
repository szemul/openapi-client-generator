<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

class StringHelper
{
    public function convertToConstantName(string $string): string
    {
        return strtoupper($this->convertToSnakeCase($this->convertToPhpName($string)));
    }

    public function convertToMethodOrVariableName(string $string): string
    {
        return $this->convertToCamelCase($this->convertToPhpName($string));
    }

    public function convertToClassName(string $string): string
    {
        return $this->convertToCamelCase($this->convertToPhpName($string), true);
    }

    public function convertToPhpName(string $string): string
    {
        $result = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
        $result = str_replace([' ', '-', '.'], '_', $result);

        $result = preg_replace('#[^A-Za-z0-9_]#', '', $result);
        if (preg_match('#^(\d+)(_*)(.+)$#', $result, $matches)) {
            $result = $matches[3] . '_' . $matches[1];
        }

        if (ctype_digit($result)) {
            $result = 'value_' . $result;
        }

        return $result;
    }

    private function convertToCamelCase(string $string, bool $capitalizeFirstCharacter = false): string
    {
        $result = str_replace(['-', '_', ' '], '', ucwords($string, '-_ '));

        if (!$capitalizeFirstCharacter) {
            $result = lcfirst($result);
        }

        return $result;
    }

    private function convertToSnakeCase(string $string): string
    {
        return ltrim(strtolower(preg_replace('#[A-Z]([A-Z](?![a-z]))*#', '_$0', $string)), '_');
    }
}
