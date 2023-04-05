<?php

use AiMuseum\Services\ArtworkGenerator;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$artworkGenerator = new ArtworkGenerator($config);

$limit = $config['GENERATOR_BATCH_LIMIT'];
$files = glob($config['FOLDER_QUEUE'] . '/*.png');
foreach ($files as $file) {
    $limit--;
    if ($limit < 0) {
        die(sprintf("This script only processes %d files at a time", $limit));
    }

    $artworkGenerator->generateArtworkData($file);

    sleep($config['GENERATOR_BATCH_SLEEP_TIME']);
}