<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum MemberStatusEnum: string
{
    case VALIDATED = 'VALIDATED';
    case NOTVALIDATED = 'NOT-VALIDATED';
}
