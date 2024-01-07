<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

class ClassHelper
{
    public function __construct(private readonly StringHelper $stringHelper)
    {
    }

    public function getActionParameterClassName(string $tag, string $operationId): string
    {
        return $this->stringHelper->convertToClassName($tag . '_' . $operationId);
    }

    public function getModelClassname(string $reference): string
    {
        return $this->stringHelper->convertToClassName(basename($reference));
    }

    public function getInlineRequestModelClassname(string $operationId): string
    {
        return $this->stringHelper->convertToClassName($operationId . 'Request');
    }

    public function getListModelClassname(string $subTypeReference): string
    {
        return $this->getModelClassname($subTypeReference) . 'List';
    }

    public function getRequestExceptionClassName(int $errorCode): string
    {
        return 'Request' . $errorCode . 'Exception';
    }
}
