<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Configuration;

class Composer
{
    private string $vendorName;
    private string $projectName;

    public function __construct(string $vendorName, string $projectName)
    {
        $this->vendorName  = $vendorName;
        $this->projectName = $projectName;
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
