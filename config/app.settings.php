<?php

declare(strict_types=1);

use Green\TomTroc\Repository\BookRepository;
use Green\TomTroc\Repository\MemberRepository;
use Green\TomTroc\Repository\MessageRepository;

return [
    'app' => [
        'name' => 'My Site',
        'memberRepository' => new MemberRepository(),
        'bookRepository' => new BookRepository(),
        'messageRepository' => new messageRepository(),
    ],
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=db',
        'username' => 'user',
        'password' => 'password',
    ],
];
