<?php

use AiMuseum\Services\S3Synchronizer;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$synchronizer = new S3Synchronizer($config['S3_ACCESS_KEY'], $config['S3_SECRET_KEY'], $config['S3_REGION']);


$s3TmpFile = sys_get_temp_dir().'/'.uniqid();
$synchronizer->downloadFileFromS3($config['S3_PUBLIC_BUCKET'], 'artworks.json', $s3TmpFile);
$s3Json = file_get_contents($s3TmpFile);
$s3Artworks = json_decode($s3Json, true);
$artworks = file_get_contents($config['ARTWORK_LIST_LOCATION']);
$s3Artworks = array_merge($s3Artworks, $artworks);
file_put_contents($s3TmpFile, $s3Artworks);


$synchronizer->uploadFileToS3($config['ARTWORK_LIST_LOCATION'], $config['S3_PUBLIC_BUCKET'], $s3TmpFile, true);
$synchronizer->uploadToS3($config['FOLDER_IMAGES'], $config['S3_PUBLIC_BUCKET'], 'images/', true);
$synchronizer->uploadToS3($config['FOLDER_PUBLIC_DATA'], $config['S3_PUBLIC_BUCKET'], 'data/', true);
$synchronizer->uploadToS3($config['FOLDER_METADATA'], $config['S3_PRIVATE_BUCKET'], 'metadata/', false);