<?php

namespace AiMuseum\Services;

class ArtworkGenerator
{
    private array $config;
    private ArtworkDescriber $artworkDescriber;
    private ExifReader $exifReader;
    private ImageOptimizer $imageOptimizer;
    private PaletteExtractor $paletteExtractor;
    private UuidGenerator $uuidGenerator;


    public function __construct(array $config)
    {
        $this->config = $config;
        $this->artworkDescriber = new ArtworkDescriber($config['API_KEY_CHATGPT'], $config['CHATGPT_PROMPT_CONFIG'], $config['CHATGPT_MODEL']);
        $this->exifReader = new ExifReader();
        $this->imageOptimizer = new ImageOptimizer($config['IMAGE_OPTIMIZATION_MODE'], $config['API_KEY_TINYPNG']);
        $this->paletteExtractor = new PaletteExtractor();
        $this->uuidGenerator = new UuidGenerator();
    }

    public function generateArtworkData($file): void
    {

        [$width, $height, $type] = getimagesize($file);

        if ($type != IMAGETYPE_PNG) {
            throw new \Exception("The file %s is not a PNG image.", $file);
        }

        $data = $this->exifReader->readMetadata($file);

        $data['id'] = $this->uuidGenerator->generate();
        $data['created_at'] = date("c", filemtime($file));
        $data['width'] = $width;
        $data['height'] = $height;

        $data['palette'] = $this->paletteExtractor->extractPalette($file);
        $paletteDescription = $this->artworkDescriber->generateColorNames($data);
        $data += $paletteDescription;

        // @todo More image analysis (Tensorflow objects and sentiments -- google api, etc.)
        // Note: tensorflow object detection (coco) does not seem to detect much.

        $description = $this->artworkDescriber->generateDescription($data);
        $data += $description;

        if ($this->config['GENERATOR_REWRITE_DESCRIPTION'] === true) {

            $description2 = $this->artworkDescriber->regenerateDescription($data);
            $data += $description2;
        }

        $this->saveImage($file, $data['id']);
        $this->saveImageBackup($file, $data['id']);
        $this->saveMetadata($data, $data['id']);
        $this->savePublicData($data, $data['id']);
    }

    public function saveImage($file, $uuid): void
    {
        $targetTextureFile = $this->config['FOLDER_IMAGES'] . '/' . $uuid . '.png';
        if (copy($file, $targetTextureFile)) {
            $this->imageOptimizer->optimize($targetTextureFile);
        } else {
            throw new \Exception(sprintf('Copy file %s to public images failed.', $file));
        }
    }

    public function saveImageBackup($file, $uuid): void
    {
        rename($file, $this->config['FOLDER_PROCESSED'].'/'.$uuid.'.png');
    }

    public function saveMetadata($data, $uuid): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->config['FOLDER_METADATA'] . '/' . $uuid . '.json', $json);
    }

    public function savePublicData($data, $uuid): void
    {
        $allowed = ['id', 'created_at', 'title', 'description', 'tags', 'palette', 'seed'];
        $publicData =  array_filter($data, fn($value, $key) => in_array($key, $allowed), ARRAY_FILTER_USE_BOTH);

        $json = json_encode($publicData, JSON_PRETTY_PRINT);
        file_put_contents($this->config['FOLDER_PUBLIC_DATA'] . '/' . $uuid . '.json', $json);
    }
}
