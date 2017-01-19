<?php

return [
    'username' => 'root',
    'password' => 'sanane123',
    'dsn' => 'mysql:host=127.0.0.1;dbname=test',
    'driver' => 'mysql',

    'attr' => [
        PDO::ATTR_PERSISTENT => true,
    ],

    'cache' => [
        'host' => '127.0.0.1',
        'port' => 11211
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

    'authentication' => [
        'login' => ['username', 'password'],
        'error_messages' => [
            'username' => 'Kullanıcı Adınızı Yanlış Girdiniz',
            'password' => 'Şifrenizi Yanlış Giridiniz'
        ]
    ],


];