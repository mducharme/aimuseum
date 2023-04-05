<?php

use AiMuseum\Services\S3Synchronizer;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$synchronizer = new S3Synchronizer($config['S3_ACCESS_KEY'], $config['S3_SECRET_KEY'], $config['S3_REGION']);

chdir($config['FOLDER_FRONTEND']);
echo getcwd();
echo exec('yarn build');

$synchronizer->uploadToS3($config['FOLDER_FRONTEND'].'/dist/', $config['S3_APP_BUCKET'], '', true);