<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Settings;

use Green\TomTroc\Core\Database\PdoDatabase;
use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Repository\BookRepository;
use Green\TomTroc\Repository\MemberRepository;
use Green\TomTroc\Repository\MessageRepository;
use RuntimeException;

class Settings
{
    private static string $defaultSettingsFile = ROOT_DIR . '/config/app.settings.php';
    private static array $settings = [];
    private static StorageInterface $dbManager;
    private static MemberRepository $memberRepository;
    private static BookRepository $bookRepository;
    private static MessageRepository $messageRepository;

    // Configuration keys constants
    public const APP_NAME = 'app.name';
    public const APP_DEV = 'app.dev';
    public const APP_TIMEZONE = 'app.timezone';
    public const APP_SECURITY_HASH_ALGO = 'app.security.hash_algo';
    public const APP_MEMBER_REPOSITORY = 'app.memberRepository';
    public const APP_BOOK_REPOSITORY = 'app.bookRepository';
    public const APP_MESSAGE_REPOSITORY = 'app.messageRepository';
    public const DB_STORAGE = 'db.storage';
    public const DB_OPTIONS = 'db.options';
    public const DB_FETCHALL_MODE = 'db.fetchall_mode';
    public const DB_FETCH_MODE = 'db.fetch_mode';
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
                Settings::get(Settings::DB_FETCHALL_MODE),
                Settings::get(Settings::DB_FETCH_MODE),
            );
            self::$dbManager->open();
        }
        self::$memberRepository = Settings::get(Settings::APP_MEMBER_REPOSITORY, new MemberRepository());
        self::$bookRepository = Settings::get(Settings::APP_BOOK_REPOSITORY, new BookRepository());
        self::$messageRepository = Settings::get(Settings::APP_MESSAGE_REPOSITORY, new MessageRepository());
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

    // Initialize de BookRepository on the settings
    public static function getBookRepository(): BookRepository
    {
        if (isset(self::$bookRepository)) {
            return self::$bookRepository;
        } else {
            throw new RuntimeException('Settings are not initialize');
        }
    }

    // Initialize de Message Repository on the settings
    public static function getMessageRepository(): MessageRepository
    {
        if (isset(self::$messageRepository)) {
            return self::$messageRepository;
        } else {
            throw new RuntimeException('Settings are not initialize');
        }
    }

    // Initialize de Member Repository on the settings
    public static function getMemberRepository(): MemberRepository
    {
        if (isset(self::$memberRepository)) {
            return self::$memberRepository;
        } else {
            throw new RuntimeException('Settings are not initialize');
        }
    }
}
