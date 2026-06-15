<?php

declare(strict_types=1);

return [
    'db' => [
        'storage' => 'mysql',
        'dsn' => 'mysql:host=localhost;dbname=tomtroc',
        'username' => 'tomtroc',
        'password' => 'tomtroc',
        'schema' => ROOT_DIR . 'tomtroc.structure.sql',
        'options' => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::FETCH_ASSOC],
    ],
];
