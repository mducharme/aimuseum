<?php

namespace AiMuseum\Services;

use League\ColorExtractor\Client as ColorExtractor;

class PaletteExtractor
{
    public function extractPalette(string $imagePath, int $colorCount = 5): array
    {
        $extractor = new ColorExtractor();
        $image = $extractor->loadPng($imagePath);
        return array_map(fn ($color) => ['hex' => $color], $image->extract($colorCount));
    }
}
