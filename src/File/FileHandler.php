<?php

declare(strict_types=1);

namespace Emul\OpenApiClientGenerator\File;

use Emul\OpenApiClientGenerator\Template\RepresentsClassInterface;
use Exception;

class FileHandler
{
    public function createDirectory(string $path): void
    {
        if (file_exists($path)) {
            return;
        }
        $directoryCreated = mkdir($path, 0777, true);

        if (!$directoryCreated) {
            throw new Exception('Failed to create ' . $path);
        }
    }

    public function saveFile(string $filePath, string $fileContent): void
    {
        $directory = dirname($filePath);

        $this->createDirectory($directory);

        file_put_contents($filePath, $fileContent);
    }

    public function saveClassTemplateToFile(RepresentsClassInterface $template): void
    {
        $filePath = $template->getDirectory() . $template->getClassName() . '.php';

        $this->saveFile($filePath, (string)$template);
    }

    public function getFileContent(string $path): string
    {
        return file_get_contents($path);
    }

    public function copyFile(string $sourcePath, $destinationPath): void
    {
        copy($sourcePath, $destinationPath);
    }
}
