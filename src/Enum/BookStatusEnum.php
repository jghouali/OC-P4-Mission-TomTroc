<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum BookStatusEnum: string
{
    case AVAILABLE = 'AVAILABLE';
    case NOTAVAILABLE = 'NOT-AVAILABLE';
}
