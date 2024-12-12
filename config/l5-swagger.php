<?php

return [
    'api' => [
        'title' => 'API Documentation',
        'description' => 'Documentación de la API para la gestión de datos de Excel',
        'version' => '1.0.0',
    ],
    'routes' => [
        'api' => 'api/documentation',
    ],
    'paths' => [
        'docs_json' => 'api-docs.json',
        'docs_yaml' => 'api-docs.yaml',
        'base' => env('L5_SWAGGER_BASE_PATH', null),
        'annotations' => [
            base_path('app/Http/Controllers'),
        ],
    ],
];

