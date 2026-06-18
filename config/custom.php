<?php

declare(strict_types=1);

return [
    'app' => [
        'timezone' => 'Indian/Reunion',
    ],
    'security' => [
        'hash_algo' => PASSWORD_BCRYPT,
    ],
    'db' => [
        'storage' => 'mysql',
        'dsn' => 'mysql:host=localhost;dbname=tomtroc',
        'username' => 'tomtroc',
        'password' => 'tomtroc',
        'schema' => ROOT_DIR . 'tomtroc.structure.sql',
        'options' => [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
        'fetchall_mode' => PDO::FETCH_ASSOC,
        'fetch_mode' => PDO::FETCH_ASSOC,
    ],
];
