<?php
namespace AiMuseum\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Transfer;
class S3Synchronizer
{
    private S3Client $s3;
    private int $concurrency;

    public function __construct(string $key, string $secret, string $region, int $concurrency=20)
    {
        // Create an S3 client
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $key,
                'secret' => $secret
            ]
        ]);
        $this->concurrency = 20;
    }

    public function uploadToS3(string $localFolder, string $bucket, string $remoteFolder)
    {
        $s3Folder = 's3://'.$bucket.'/'.$remoteFolder;

        // Sync the folders using the S3 client
        try {
            $this->s3->registerStreamWrapper();
            $options = [
                'params' => ['ACL' => 'public-read'],
                'concurrency' => $this->concurrency,
                'debug' => true
            ];
            $manager = new Transfer($this->s3, $localFolder, $s3Folder, $options);
            $manager->transfer();
            echo "Sync successful!";
        } catch (S3Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function downloadFromS3(string $bucket, string $remoteFolder, string $localFolder)
    {
        $s3Folder = 's3://'.$bucket.'/'.$remoteFolder;

        // Sync the folders using the S3 client
        try {
            $this->s3->registerStreamWrapper();
            $options = [
                'concurrency' => $this->concurrency,
                'debug' => true
            ];
            $manager = new Transfer($this->s3, $s3Folder, $localFolder, $options);
            $manager->transfer();
            echo "Sync successful!";
        } catch (S3Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}