<?php

namespace AiMuseum\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DataCleaner
{
    public function removeDataFilesWithoutImages(string $dataDir, string $texturesDir): void
    {
        $texturesUuids = $this->getUuidsFromDirectory($texturesDir, 'png');
        $this->removeFilesWithoutMatchingUuid($dataDir, $texturesUuids, 'json');
    }

    public function removeImagesFilesWithoutData(string $dataDir, string $texturesDir): void
    {
        $dataUuids = $this->getUuidsFromDirectory($dataDir, 'json');
        $this->removeFilesWithoutMatchingUuid($texturesDir, $dataUuids, 'png');
    }

    public function getUuidsFromDirectory(string $directory, string $extension): array
    {
        $uuids = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                $uuids[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        return $uuids;
    }

    public function removeFilesWithoutMatchingUuid(string $directory, array $uuids, string $extension): void
    {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === $extension) {
                $uuid = pathinfo($file, PATHINFO_FILENAME);
                if (!in_array($uuid, $uuids)) {
                    unlink($file);
                }
            }
        }
    }
}
