<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Settings;

use Green\TomTroc\Controller\BookController;
use Green\TomTroc\Controller\HomeController;
use Green\TomTroc\Controller\MemberController;
use Green\TomTroc\Core\Database\PdoDatabase;
use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Router\Router;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Manager\BookManager;
use Green\TomTroc\Manager\MemberManager;
use Green\TomTroc\Manager\MessageManager;
use Green\TomTroc\Repository\BookRepository;
use Green\TomTroc\Repository\MemberRepository;
use Green\TomTroc\Repository\MessageRepository;
use RuntimeException;

class Settings
{
    private static string $defaultSettingsFile = ROOT_DIR . '/config/app.settings.php';
    private static array $settings = [];
    private static StorageInterface $dbManager;
    private static BookManager $bookManager;
    private static MemberManager $memberManager;
    private static MessageManager $messageManager;
    private static BookRepository $bookRepository;
    private static MemberRepository $memberRepository;
    private static MessageRepository $messageRepository;
    private static AuthentificationService $authentificationService;
    private static Router $router;
    private static HomeController $homeController;
    private static BookController $bookController;
    private static MemberController $memberController;
    // Configuration keys constants
    public const APP_NAME = 'app.name';
    public const APP_DEV = 'app.dev';
    public const APP_TIMEZONE = 'app.timezone';
    public const APP_SECURITY_HASH_ALGO = 'app.security.hash_algo';
    public const APP_MEMBER_REPOSITORY = 'app.memberRepository';
    public const APP_BOOK_REPOSITORY = 'app.bookRepository';
    public const APP_MESSAGE_REPOSITORY = 'app.messageRepository';
    public const APP_MEMBER_MANAGER = 'app.memberManager';
    public const APP_BOOK_MANAGER = 'app.bookManager';
    public const APP_MESSAGE_MANAGER = 'app.messageManager';
    public const APP_SESSION_SERVICE = 'app.sessionService';
    public const APP_ROUTER = 'app.router';
    public const APP_HOME_CONTROLLER = 'app.homeController';
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
        // Repositories
        self::$memberRepository = new MemberRepository(
            self::$dbManager
        );
        self::$bookRepository = new BookRepository(
            self::$dbManager,
            self::$memberRepository
        );
        self::$messageRepository = new MessageRepository(
            self::$dbManager,
            self::$memberRepository
        );

        // Services
        self::$authentificationService = new AuthentificationService();
        // Managers
        self::$memberManager = new MemberManager(
            self::$memberRepository,
            self::$authentificationService
        );
        self::$bookManager = new BookManager(
            self::$bookRepository,
            self::$authentificationService
        );
        self::$messageManager = new MessageManager(
            self::$messageRepository,
            self::$authentificationService
        );
        // Router
        self::$router = new Router();
        // Controllers
        self::$homeController = new HomeController(
            self::$bookManager
        );
        self::$bookController = new BookController(
            self::$bookManager,
            self::$memberManager
        );
        self::$memberController = new MemberController(
            self::$bookManager,
            self::$memberManager,
            self::$authentificationService
        );
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
            throw new RuntimeException('self::$dbManager is not initialized');
        }
    }

    // Initialize de BookRepository on the settings
    public static function getBookRepository(): BookRepository
    {
        if (isset(self::$bookRepository)) {
            return self::$bookRepository;
        } else {
            throw new RuntimeException('self::$bookRepository is not initialized');
        }
    }

    // Initialize de Message Repository on the settings
    public static function getMessageRepository(): MessageRepository
    {
        if (isset(self::$messageRepository)) {
            return self::$messageRepository;
        } else {
            throw new RuntimeException('self::$messageRepository is not initialized');
        }
    }

    // Initialize de Member Repository on the settings
    public static function getMemberRepository(): MemberRepository
    {
        if (isset(self::$memberRepository)) {
            return self::$memberRepository;
        } else {
            throw new RuntimeException('self::$memberRepository is not initialized');
        }
    }

    // Initialize de Authentification Service on the settings
    public static function getAuthentificationService(): AuthentificationService
    {
        if (isset(self::$authentificationService)) {
            return self::$authentificationService;
        } else {
            throw new RuntimeException('self::$authentificationService is not initialized');
        }
    }

    // Initialize de BookManager on the settings
    public static function getBookManager(): BookManager
    {
        if (isset(self::$bookManager)) {
            return self::$bookManager;
        } else {
            throw new RuntimeException('self::$bookManager is not initialized');
        }
    }

    // Initialize de Message Manager on the settings
    public static function getMessageManager(): MessageManager
    {
        if (isset(self::$messageManager)) {
            return self::$messageManager;
        } else {
            throw new RuntimeException('self::$messageManager is not initialized');
        }
    }

    // Initialize de Member Manager on the settings
    public static function getMemberManager(): MemberManager
    {
        if (isset(self::$memberManager)) {
            return self::$memberManager;
        } else {
            throw new RuntimeException('self::$memberManager is not initialized');
        }
    }

    // Initialize de Router on the settings
    public static function getRouter(): Router
    {
        if (isset(self::$router)) {
            return self::$router;
        } else {
            throw new RuntimeException('self::$router is not initialized');
        }
    }

    // Initialize de HomeController on the settings
    public static function getHomeController(): HomeController
    {
        if (isset(self::$homeController)) {
            return self::$homeController;
        } else {
            throw new RuntimeException('self::$homeController is not initialized');
        }
    }

    // Initialize de BookController on the settings
    public static function getBookController(): BookController
    {
        if (isset(self::$bookController)) {
            return self::$bookController;
        } else {
            throw new RuntimeException('self::$bookController is not initialized');
        }
    }

    // Initialize de MemberController on the settings
    public static function getMemberController(): MemberController
    {
        if (isset(self::$memberController)) {
            return self::$memberController;
        } else {
            throw new RuntimeException('self::$memberController is not initialized');
        }
    }
}
