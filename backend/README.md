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