<?php

return [

    'connections' => [

        'default' => 'localhost', // ön tanımlı bağlantı.

        'localhost' => [

            'username' => 'root',
            'password' => 'sanane123',
            'dsn' => 'mysql:host=127.0.0.1;dbname=test',
            'driver' => 'mysql',

            'attr' => [
                PDO::ATTR_PERSISTENT => true,
            ],

        ],

    ],


    'cache' => [

        'driver' => 'memcache', // also supports redis

        'memcache' => [
            'host' => '127.0.0.1',
            'port' => 11211
        ],

        'redis' => [
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]
    ],

    'logging' => [
        'open' => true,
    ],

    'policies' => [
        //
    ],

    'migrations' => [
        // put here your primary migrations
    ],

    'PRIVATE_KEY' => md5('YOUR KEY'),
];
