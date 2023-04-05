<?php

use AiMuseum\Services\ArtworkDescriber;
use AiMuseum\Services\ExifReader;
use AiMuseum\Services\ImageOptimizer;
use AiMuseum\Services\PaletteExtractor;
use AiMuseum\Services\DataCleaner;

global $FOLDER_IMAGES;
global $FOLDER_METADATA;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$dataCleaner = new DataCleaner();
$dataCleaner->removeDataFilesWithoutTextures($FOLDER_METADATA, $FOLDER_IMAGES);
$dataCleaner->removeTexturesFilesWithoutData($FOLDER_METADATA, $FOLDER_IMAGES);