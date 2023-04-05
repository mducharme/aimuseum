<?php

use AiMuseum\Services\S3Synchronizer;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$synchronizer = new S3Synchronizer($config['S3_ACCESS_KEY'], $config['S3_SECRET_KEY'], $config['S3_REGION']);

$synchronizer->downloadFromS3($config['S3_PUBLIC_BUCKET'], 'images/', $config['FOLDER_IMAGES']);
$synchronizer->downloadfromS3($config['S3_PUBLIC_BUCKET'], 'data/', $config['FOLDER_PUBLIC_DATA']);
$synchronizer->downloadFromS3($config['S3_PRIVATE_BUCKET'], 'metadata/',$config['FOLDER_METADATA']);