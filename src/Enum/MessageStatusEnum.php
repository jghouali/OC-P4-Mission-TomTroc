<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum MessageStatusEnum: string
{
    case READ = 'READ';
    case NOTREAD = 'NOT-READ';
}
