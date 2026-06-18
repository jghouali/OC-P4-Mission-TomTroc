<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Lib;

use DateTimeZone;
use Green\TomTroc\Core\Settings\Settings;
use RuntimeException;

class Locales
{
    public static function getLocalDateTime(?string $datetime = null): \DateTime
    {
        if (Settings::get(Settings::APP_TIMEZONE) === null) {
            throw new RuntimeException(("TimeZone isn\'t set, please configure app.timezone"));
        }

        if (!isset($datetime)) {
            $datetime = 'now';
        }

        $result = date_create(
            $datetime,
            new DateTimeZone(Settings::get(Settings::APP_TIMEZONE))
        );

        if (!$result) {
            throw new RuntimeException(("Date : $datetime isn't valid"));
        }
        return $result;
    }

    public static function getLocalFormattedDateTime(?string $datetime = null): string
    {
        if (Settings::get(Settings::APP_TIMEZONE) === null) {
            throw new RuntimeException(("TimeZone isn\'t set, please configure app.timezone"));
        }

        if (!isset($datetime)) {
            $datetime = 'now';
        }

        $result = date_create(
            $datetime,
            new DateTimeZone(Settings::get(Settings::APP_TIMEZONE))
        );

        if (!$result) {
            throw new RuntimeException(("Date : $datetime isn't valid"));
        }
        return date_format($result, 'Y-m-d H:i:s');
    }
}
