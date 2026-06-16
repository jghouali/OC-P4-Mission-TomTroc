<?php

declare(strict_types=1);

namespace Green\TomTroc\Enum;

enum MessageStatusEnum: int
{
    case READ = 1;
    case NOTREAD = 0;
}
