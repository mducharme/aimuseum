<?php
/**
 * Delete the images that don't have associated data and data that don't have associated images.
 */
use AiMuseum\Services\DataCleaner;

global $FOLDER_IMAGES;
global $FOLDER_METADATA;

require __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

$dataCleaner = new DataCleaner();
$dataCleaner->removeDataFilesWithoutImages($config['FOLDER_METADATA'], $config['FOLDER_IMAGES']);
$dataCleaner->removeImagesFilesWithoutData($config['FOLDER_METADATA'], $config['FOLDER_IMAGES']);