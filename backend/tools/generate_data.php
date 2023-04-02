<?php

use AiMuseum\Services\ArtworkGenerator;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';
$toolsConfig = require_once __DIR__ . '/../config/tools.php';

$artworkGenerator = new ArtworkGenerator($config, $toolsConfig);

$limit = $toolsConfig['GENERATOR_BATCH_LIMIT'];
$files = glob($toolsConfig['FOLDER_QUEUE'] . '/*.png');
foreach ($files as $file) {
    $limit--;
    if ($limit < 0) {
        die(sprintf("This script only processes %d files at a time", $limit));
    }

    $artworkGenerator->generateArtworkData($file);

    sleep($toolsConfig['GENERATOR_BATCH_SLEEPTIME']);
}