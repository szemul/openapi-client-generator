<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

class ClassHelper
{
    private StringHelper $stringHelper;

    public function __construct(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    public function getActionParameterClassName(string $tag, string $operationId): string
    {
        return $this->stringHelper->convertToClassName($tag . '_' . $operationId);
    }

    public function getModelClassname(string $reference): string
    {
        return basename($reference);
    }

    public function getListModelClassname(string $subTypeReference): string
    {
        return $this->getModelClassname($subTypeReference) . 'List';
    }
}
