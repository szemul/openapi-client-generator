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
        LocationHelper $locationHelper,
        StringHelper $stringHelper,
        string $enumName,
        string ...$values
    ) {
        parent::__construct($locationHelper, $stringHelper);

        $this->enumName = $enumName;
        $this->values   = $values;
    }

    public function getDirectory(): string
    {
        return $this->getLocationHelper()->getEnumPath();
    }

    public function getNamespace(): string
    {
        return $this->getLocationHelper()->getEnumNamespace();
    }

    protected function getShortClassName(): string
    {
        return $this->getStringHelper()->convertToClassName($this->enumName);
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
            $constName = $this->getStringHelper()->convertToConstantName($value);

            $constants[] = '    public const ' . $constName . " = '" . $value . "';";
        }

        return implode(PHP_EOL, $constants);
    }

    private function getCreatorMethods(): string
    {
        $methods = [];

        foreach ($this->values as $value) {
            $methodName = $this->getStringHelper()->convertToMethodOrVariableName($value);
            $constName  = $this->getStringHelper()->convertToConstantName($value);

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
            $constName   = $this->getStringHelper()->convertToConstantName($value);
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
