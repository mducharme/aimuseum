<?php

use AiMuseum\Services\S3Synchronizer;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';
$toolsConfig = require_once __DIR__ . '/../config/tools.php';

$synchronizer = new S3Synchronizer($toolsConfig['S3_ACCESS_KEY'], $toolsConfig['S3_SECRET_KEY'], $toolsConfig['S3_REGION']);

$synchronizer->uploadToS3($config['FOLDER_IMAGES'], $toolsConfig['S3_PUBLIC_BUCKET'], 'images/');
$synchronizer->uploadToS3($config['FOLDER_PUBLIC_DATA'], $toolsConfig['S3_PUBLIC_BUCKET'], 'data/');
$synchronizer->uploadToS3($config['FOLDER_METADATA'], $toolsConfig['S3_PRIVATE_BUCKET'], 'metadata/');