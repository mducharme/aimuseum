<?php
require __DIR__ . '/../vendor/autoload.php';

use AiMuseum\Services\ArtworkLoader;

$config = require __DIR__ . '/../config/config.php';

$artworkLoader = new ArtworkLoader($config['FOLDER_METADATA']);
$artworks = $artworkLoader->getListAll();

$json = json_encode($artworks, JSON_PRETTY_PRINT);
var_dump($json);
var_dump($config['ARTWORK_LIST_LOCATION']);
file_put_contents($config['ARTWORK_LIST_LOCATION'], $json);