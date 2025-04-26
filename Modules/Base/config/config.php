<?php

declare(strict_types=1);

return [
    'name' => 'Base',
    'api_version' => 'v1',
    'module_prefix' => [
        'user' => 'user',
        'base' => 'base',
        'auth' => 'auth',
    ],
    'pagination' => [
        'default' => 10,
        'max' => 100,
    ],
];
