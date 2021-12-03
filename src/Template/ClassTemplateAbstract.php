<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

abstract class ClassTemplateAbstract extends TemplateAbstract implements RepresentsClassInterface
{
    abstract protected function getShortClassName(): string;

    public function getClassName(bool $fqcn = false): string
    {
        $result = '';
        if ($fqcn) {
            $result = $this->getNamespace() . '\\';
        }

        return $result . $this->getShortClassName();
    }

}
