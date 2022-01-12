<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\Helper;

use Exception;

class CommandHelper
{
    public function execute(string $command): void
    {
        $output     = null;
        $resultCode = null;

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new Exception('Failed to execute ' . $command . PHP_EOL . implode(PHP_EOL, $output));
        }
    }
}
