<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Template;

interface RepresentsClassInterface
{
    public function getNamespace(): string;

    public function getClassName(): string;

    public function getDirectory(): string;
}
