<?php

namespace AiMuseum\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use function strtr;

class ArtworkDescriber
{
    private Client $client;
    private array $promptConfig;
    private string $model;

    public function __construct(string $apiKey, array $promptConfig, $model = 'gpt-3.5-turbo')
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey
            ]
        ]);

        $this->promptConfig = $promptConfig;
        $this->model = $model;
    }

    public function generateColorNames(array $artworkData)
    {
        $hexValues = array_map(fn ($color) => $color['hex'], $artworkData['palette']);
        $palette = implode(', ', $hexValues);
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->promptConfig['palette-generation']['system']
                        ],
                        [
                            'role' => 'user',
                            'content' => strtr($this->promptConfig['palette-generation']['user'], ['{palette}' => $palette])
                        ]
                    ]
                ]
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return [
                'palette' => $this->extractColorNames($responseData['choices'][0]['message']['content'])
            ];
        } catch (ClientException $e) {
            echo "API request failed: " . $e->getMessage();
        }
    }

    public function generateDescription(array $artworkData): array
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->promptConfig['description-generation']['system']
                    ],
                    [
                        'role' => 'user',
                        'content' => strtr($this->promptConfig['description-generation']['user'], [
                            '{prompt}' => $artworkData['prompt'],
                            '{palette_names}'=> implode(', ', array_column($artworkData['palette'], 'name'))
                        ])
                    ]
                ]
            ]
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);
        return $this->splitArtworkString($responseData['choices'][0]['message']['content']);

    }

    public function regenerateDescription(array $artworkData): array
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->promptConfig['description-regeneration']['system']
                    ],
                    [
                        'role' => 'user',
                        'content' => strtr($this->promptConfig['description-regeneration']['user'], [
                            '{description}' => $artworkData['description']
                        ])
                    ]
                ]
            ]
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);
        return [
            'description' => $responseData['choices'][0]['message']['content']
        ];
    }

   private function extractColorNames(string $string): array
   {
       $matches = [];
       $pattern = '/#(?<hex>[0-9A-Fa-f]{6})\s*:\s*(?<name>.+?)(?:\n|$)/m';
       preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);

       return array_map(fn ($match) => [
           'hex' => '#' . strtoupper(trim($match['hex'])),
           'name' => ucwords(strtolower(trim($match['name']))),
       ], $matches);
   }

    private function splitArtworkString(string $string): array
    {
        $matches = [];
        $pattern = '/Title:\s*(?<title>.+?)\n+Description:\s*(?<description>.+?)\n+Tags:\s*(?<tags>.+?)\s*$/s';
        preg_match($pattern, $string, $matches);

        return [
            'title' =>  ucwords(strtolower(trim(($matches['title'])))) ?? null,
            'description' => trim($matches['description']) ?? null,
            'tags' => isset($matches['tags']) ? array_map(fn ($tag) => ucwords(strtolower(trim($tag))), explode(',', $matches['tags'])) : [],
        ];
    }
}
