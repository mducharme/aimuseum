## System Requirements

### PHP

- Version 7.4+ (8.1+ recommended) 
- The `gd` extension is required (for palette extraction and other image operations)
- The `json` extension is required (for various data operations)

```json
sudo apt install php-cli php-json php-gd
```

#### PHP Libraries

- `guzzlehttp/guzzled` is required (for various http operations / api calls)
- `league/color-extractor` is required (for plaette extractions)
- `tinyfy/tinify` is required (for png compression)
    - An API key from tinypng.com is needed. See `config/config.php`.
  
Install them with:
```json
composer install
```
### Node

### Node Libraries

### OpenAI API Key

- An API key from openapi.com is required to use ChatGPT.
  - To use the GPT-4 model, it must be enabled on the key. Not tested.
  - The default model is `gpt-3.5-turbo`.
  - See config/tools.php for details.

## Starting the backend

> First make sure you copy and edit:
> - `config/tools.sample.php` to `config/tools.php`
> - `config/config.sample.php` to `config/config.php`

To start development server on http://localhost:9090

```
composer serve
```

## Generating the artwork data

Place Stable-Diffusion generated PNG files in the queue folder (default is `images/queue/`).

Then run 
```
php tools/generate_data.php
``` 
to generate their meta information from various tools like ChatGPT.
The resulting optimized textures will be in `public/textures/`, the private metadata in `data/` and a copy of the original files (with EXIF data intact) in `images/originals`

> All those folders can be changed in the config files.