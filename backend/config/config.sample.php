<?php

return [
    'FOLDER_IMAGES' => realpath(__DIR__ . '/../public/images/'),
    'FOLDER_METADATA'=>realpath(__DIR__ . '/../data/'),

    'CACHE_BROWSER_ARTWORK_INFO_TTL' => 3600*24*30,
    'CACHE_BROWSER_ARTWORK_LIST_TTL' => 3600*24,

    'CACHE_SERVER_ARTWORK_INFO_TTL' => 3600*24*30,
    'CACHE_SERVER_ARTWORK_LIST_TTL' => 3600*24
];