<?php
declare(strict_types=1);

return [
    'default' => 'mysql',

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../storage/database.sqlite',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => 'db_coockie',
            'dbname' => 'db_coockie',
            'user' => 'root',
            'password' => 'root',
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'pgsql',
            'dbname' => 'app',
            'user' => 'app',
            'password' => 'secret',
        ],
    ],
];