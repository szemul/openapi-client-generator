<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template\Model;

use Emul\OpenApiClientGenerator\Helper\LocationHelper;
use Emul\OpenApiClientGenerator\Helper\StringHelper;
use Emul\OpenApiClientGenerator\Template\ClassTemplateAbstract;

class EnumTemplate extends ClassTemplateAbstract
{
    private string $enumName;
    /** @var string[] */
    private array $values;

    public function __construct(
        private readonly LocationHelper $locationHelper,
        private readonly StringHelper $stringHelper,
        string $enumName,
        string ...$values
    ) {
        $this->enumName = $enumName;
        $this->values   = $values;
    }

    public function getDirectory(): string
    {
        return $this->locationHelper->getEnumPath();
    }

    public function getNamespace(): string
    {
        return $this->locationHelper->getEnumNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->stringHelper->convertToClassName($this->enumName);
    }

    public function __toString(): string
    {
        return <<<ENUM
            <?php
            declare(strict_types=1);
            
            namespace {$this->getNamespace()};
            
            use Emul\Enum\EnumAbstract;

            class {$this->getClassName()} extends EnumAbstract
            {
            {$this->getConstants()}
            {$this->getCreatorMethods()}
            {$this->getPossibleValuesMethod()}
            }
            ENUM;
    }

    private function getConstants(): string
    {
        $constants = [];

        foreach ($this->values as $value) {
            $constName = $this->stringHelper->convertToConstantName($value);

            $constants[] = '    public const ' . $constName . " = '" . $value . "';";
        }

        return implode(PHP_EOL, $constants);
    }

    private function getCreatorMethods(): string
    {
        $methods = [];

        foreach ($this->values as $value) {
            $methodName = $this->stringHelper->convertToMethodOrVariableName($value);
            $constName  = $this->stringHelper->convertToConstantName($value);

            $methods[] = <<<CREATOR
                public static function {$methodName}(): self
                {
                    return new self(self::{$constName});
                }
            CREATOR;
        }

        return implode(PHP_EOL, $methods);
    }

    private function getPossibleValuesMethod(): string
    {
        $constants    = [];
        $constantList = '';

        foreach ($this->values as $value) {
            $constName   = $this->stringHelper->convertToConstantName($value);
            $constants[] = 'self::' . $constName;
        }
        $constantList = implode(', ', $constants);

        return <<<METHOD
            protected static function getPossibleValues(): array
            {
                return [{$constantList}];
            }
        METHOD;
    }
}
