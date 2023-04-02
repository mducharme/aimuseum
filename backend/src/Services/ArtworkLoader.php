<?php

namespace AiMuseum\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ArtworkLoader
{
    private string $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    public function getListAll(): array
    {
        $cacheKey = 'museum_artwork_list';
        $cacheDuration = 3600;

        $apcuEnabled = extension_loaded('apcu') && ini_get('apc.enabled');

        if ($apcuEnabled && apcu_exists($cacheKey)) {
            $pngFiles = apcu_fetch($cacheKey);
        } else {
            $artworks = [];
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->dataFolder)) as $file) {
                if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $artworks[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
            if ($apcuEnabled) {
                apcu_store($cacheKey, $artworks, $cacheDuration);
            }
        }
        return $artworks;
    }

    public function getMetadata(string $id): ?array
    {
        $cacheKey = 'museum_artwork_data_'.$id;
        $cacheDuration = 3600;

        $dataFile = realpath($this->dataFolder.'/'.$id.'.json');
        $artworkInfo = [];

        $apcuEnabled = extension_loaded('apcu') && ini_get('apc.enabled');

        if ($apcuEnabled && apcu_exists($cacheKey)) {
            $artworkInfo = apcu_fetch($cacheKey);
        } else {
            if (!file_exists($dataFile)) {
                return null;
            } else {
                $artworkInfo += json_decode(file_get_contents($dataFile), true);
            }
            // Cache the artwork data using APCu if it's available
            if (extension_loaded('apcu')) {
                apcu_store($cacheKey, $artworkInfo, $cacheDuration);
            }
        }

        return $artworkInfo;
    }

    public function getPublicData(string $id): ?array
    {
        $data =  $this->getMetadata();
        if ($data === null) {
            return null;
        }

        return array_filter($data, function ($value, $key) {
            return in_array($key, ['id', 'created_at', 'title', 'description', 'tags', 'palette', 'seed']);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
