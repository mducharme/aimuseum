<?php

use AiMuseum\Services\ArtworkDescriber;
use AiMuseum\Services\ExifReader;
use AiMuseum\Services\ImageOptimizer;
use AiMuseum\Services\PaletteExtractor;
use AiMuseum\Services\DataCleaner;

global $FOLDER_TEXTURES;
global $FOLDER_DATA;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$dataCleaner = new DataCleaner();
$dataCleaner->removeDataFilesWithoutTextures($FOLDER_DATA, $FOLDER_TEXTURES);
$dataCleaner->removeTexturesFilesWithoutData($FOLDER_DATA, $FOLDER_TEXTURES);