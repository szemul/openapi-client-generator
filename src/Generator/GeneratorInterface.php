<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Generator;

interface GeneratorInterface
{
    public function generate(): void;
}
