<?php

namespace AiMuseum\Services;

class ExifReader
{
    public function __construct()
    {
        if (exec('which exiftool') == false) {
            throw new \RuntimeException('The command "exiftool" must be installed on the system.');
        }
    }

    public function readMetadata($imagePath)
    {
        $inputString = `exiftool -PNG:Parameters $imagePath`;

        $pattern = '/Parameters\s*:\s*(?<prompt>[^\.]*).*Negative prompt\s*:\s*(?<negative_prompt>[^\.]*).*Steps\s*:\s*(?<steps>\d+).*Sampler\s*:\s*(?<sampler>[^\,]*).*CFG scale\s*:\s*(?<cfg_scale>[\d\.]+).*Seed\s*:\s*(?<seed>\d+).*Face restoration\s*:\s*(?<face_restoration>[^\,]*).*Size\s*:\s*(?<generated_size>[\dx]+).*Model hash\s*:\s*(?<model_hash>[^\,]*).*Model\s*:\s*(?<model>[^\n]*)/s';
        preg_match($pattern, $inputString, $matches);

        $prompt = trim($matches['prompt'] ?? '');
        $negativePrompt = trim($matches['negative_prompt'] ?? '');
        $steps = (int)($matches['steps'] ?? 0);
        $sampler = trim($matches['sampler'] ?? '');
        $cfgScale = (float)($matches['cfg_scale'] ?? 0.0);
        $seed = (int)($matches['seed'] ?? 0);
        $faceRestoration = trim($matches['face_restoration'] ?? '');
        $generatedSize = trim($matches['generated_size'] ?? '');
        $modelHash = trim($matches['model_hash'] ?? '');
        $model = trim($matches['model'] ?? '');


        return [
            'prompt' => $prompt,
            'negative_prompt' => $negativePrompt,
            'steps' => $steps,
            'sampler' => $sampler,
            'cfg_scale' => $cfgScale,
            'seed' => $seed,
            'face_restoration' => $faceRestoration,
            'generated_size' => $generatedSize,
            'model_hash' => $modelHash,
            'model' => $model
        ];
    }
}
