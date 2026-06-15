<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Settings;

use Green\TomTroc\Core\Database\PdoDatabase;
use Green\TomTroc\Core\Database\StorageInterface;
use RuntimeException;

class Settings
{
    private static string $defaultSettingsFile = ROOT_DIR . '/config/app.settings.php';
    private static array $settings = [];
    private static StorageInterface $dbManager;

    // Configuration keys constants
    public const APP_NAME = 'app.name';
    public const DB_STORAGE = 'db.storage';
    public const DB_OPTIONS = 'db.options';
    public const DB_DSN = 'db.dsn';
    public const DB_USER = 'db.username';
    public const DB_PASSWORD = 'db.password';

    // This function need to be call first to use Settings
    public static function initialize()
    {
        if (self::get(Settings::DB_STORAGE) === 'mysql') {
            self::$dbManager = new PdoDatabase(
                Settings::get(Settings::DB_DSN),
                Settings::get(Settings::DB_USER),
                Settings::get(Settings::DB_PASSWORD),
                Settings::get(Settings::DB_OPTIONS),
            );
            self::$dbManager->open();
        }
    }

    // Support multiple configuration files, last one overwite precedent keys
    public static function addSettingsFile(string $settingsFile): void
    {
        if (is_file($settingsFile)) {
            $settings = require($settingsFile);
            self::$settings = array_merge(self::$settings, $settings);
        }
    }

    // Get the whole configuration
    public static function getSettings(): array
    {
        if (empty(self::$settings)) {
            if (is_file(self::$defaultSettingsFile)) {
                self::$settings = require(self::$defaultSettingsFile);
            } else {
                throw new RuntimeException(ROOT_DIR . '/config/app.settings.php is not present');
            }
        }

        return self::$settings;
    }

    // Get one key or default
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::getSettings();
        $parse = explode('.', $key);

        foreach ($parse as $i) {
            if (!is_array($setting) || !array_key_exists($i, $setting)) {
                return $default;
            }
            $setting = $setting[$i];
        }

        return $setting;
    }

    // Initialize de DbManager configurated on the settings
    public static function getDbManager(): StorageInterface
    {
        if (isset(self::$dbManager)) {
            return self::$dbManager;
        } else {
            throw new RuntimeException('Settings are not initialize');
        }
    }
}
