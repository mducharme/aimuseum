<?php

return [
    'FOLDER_IMAGES' => realpath(__DIR__ . '/../public/images/'),
    'FOLDER_PUBLIC_DATA' => realpath(__DIR__ . '/../public/data/'),
    'FOLDER_METADATA'=>realpath(__DIR__ . '/../data/'),
    'FOLDER_QUEUE' => realpath(__DIR__ . '/../images/queue/'),
    'FOLDER_PROCESSED' => realpath(__DIR__ . '/../images/processed/'), // Keep backup of originals,

    'CACHE_BROWSER_ARTWORK_INFO_TTL' => 3600*24*30,
    'CACHE_BROWSER_ARTWORK_LIST_TTL' => 3600*24,

    'CACHE_SERVER_ARTWORK_INFO_TTL' => 3600*24*30,
    'CACHE_SERVER_ARTWORK_LIST_TTL' => 3600*24,

    'API_KEY_TINYPNG' => '',
    'API_KEY_CHATGPT' => '',

    'S3_ACCESS_KEY' => '',
    'S3_SECRET_KEY' => '',
    'S3_REGION' => 'us-east-1',
    'S3_PUBLIC_BUCKET' => '',
    'S3_PRIVATE_BUCKET' => '', // Full file metadata

    'CHATGPT_MODEL' => 'gpt-3.5-turbo', // Use GPT4 for a better but slower model, if your API key enables it

    'CHATGPT_PROMPT_CONFIG' => [
        'palette-generation' => [
            'system' => 'You are a helpful assistant who provides color names from hexadecimal values.',
            'user' => ' For every hex code I send you, you should give me a meaningful and descriptive color name that represents the code.
    
                Your output will be used in code so it is very important you only return the information I need in this exact format: The hexcode and the full name on every line. 
                There should be a blank line at the end.
                
                Example:
                
                #000000: Absolute black
                #FF0000: Bright red
                
                The colors: {palette}'
        ],
        'description-generation' => [
            'system' => 'You are a helpful assistant who is an expert at generating artwork description for museums.',
            'user' => 'Generate a title, full (long) description and 5 tags of a museum artwork. 
                                        
                Your response will be parsed by software, so return it exactly in the following format:
                Title: The title of the artwork
                Description: The full (long) description of the artwork you generated
                Tags: The 5 tags you generated for this artwork (do not use hashtag symbol)
                
                Here is the prompt that was used to generate it. Use this prompt to generate the information:
    
                "{prompt}"
                
                The main color palette used: {palette_names}'
        ],
        'description-regeneration' => [
            'system' => 'You are a helpful assistant who is an expert at generating artwork description for museums.',
            'user' => 'Rewrite this description:
            
                {description}
                
                Please rewrite the description with a higher english language level.
                
                Your output will be used in code so it is very important you only return the description, nothing else.'
        ],
        'prompt-generation' => [

        ]
    ],

    'IMAGE_OPTIMIZATION_MODE' => 'none', // optipng, tiny or none




    'GENERATOR_REWRITE_DESCRIPTION' => false, // Ask for a rewrite (2nd description) after generating a description

    'GENERATOR_BATCH_LIMIT' => 10,
    'GENERATOR_BATCH_SLEEP_TIME' => 2
];