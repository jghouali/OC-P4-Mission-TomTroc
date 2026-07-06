<?php

declare(strict_types=1);

use Green\TomTroc\Core\Settings\Settings;

if (!defined('NOTIFICATION_COUNT')) {
    define('NOTIFICATION_COUNT', Settings::getMessageController()->getNotificationCount());
}
