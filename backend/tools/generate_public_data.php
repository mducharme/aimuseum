<?php
/**
 * (Re)generate all the public data from the artwork's metadata.
 */
use AiMuseum\Services\ArtworkGenerator;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$artworkGenerator = new ArtworkGenerator($config);

$files = glob($config['FOLDER_METADATA'] . '/*.json');
foreach ($files as $file) {
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    $uuid = pathinfo($file, PATHINFO_FILENAME);
    $artworkGenerator->savePublicData($data, $uuid);
}