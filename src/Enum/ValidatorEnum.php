<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum ValidatorEnum: string
{
    case bcryptHash = 'bcryptHash';
    case uploadFile = 'uploadFile';
    case imagePath = 'imagePath';
    case email = 'email';
    case humanDate = 'humanDate';
    case intCounter = 'intCounter';
    case textContent50 = 'textContent50';
    case textContent150 = 'textContent150';
    case textContent2000 = 'textContent2000';
}
