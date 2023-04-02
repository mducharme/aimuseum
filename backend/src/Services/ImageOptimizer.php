<?php

namespace AiMuseum\Services;

use Tinify;

class ImageOptimizer
{
    private string $optimizer;

    public function __construct(string $optimizer='tiny', ?string $tinyPngApiKey=null)
    {
        if ($optimizer == 'tiny' && !is_null($tinyPngApiKey)) {
            Tinify\setKey($tinyPngApiKey);
        } elseif ($optimizer == 'optipng' && exec('which optipng') == false) {
            throw new \RuntimeException('The command "optipng" must be installed on the system.');
        }
        $this->optimizer = $optimizer;
    }

    public function optimize(string $imagePath, ?string $target=null): void
    {

        switch($this->optimizer) {
            case 'tiny':
                $target ??= $imagePath;
                $source = Tinify\fromFile($imagePath);
                $source->toFile($target);
                break;

            case 'optipng':

                /*if ($target != $imagePath) {
                    $cmd = exec('optipng -o7 ' . realpath($imagePath));
                } else {
                    $cmd = exec('optipng -o7 '. realpath($imagePath).' --out='.realpath($target));
                }*/

                break;
        }

    }
}
