<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

class Composer
{
    public function __construct(
        private readonly string $vendorName,
        private readonly string $projectName
    ) {
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }
}
