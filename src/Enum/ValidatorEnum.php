<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum ValidatorEnum: string
{
    case alphanumeric_50 = '50alphanumeric_-';
    case alphanumeric_150 = '150alphanumeric_-';
    case bcryptHash = 'bcryptHash';
    case uploadFile = 'uploadFile';
    case email = 'email';
    case humanDate = 'humanDate';
    case intCounter = 'intCounter';
    case textContent = 'textContent';
}
