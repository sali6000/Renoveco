<?php

namespace Core\Routing;

use Config\AppConfig;
use Core\Support\DebugHelper;

class RouteCache
{
    private static $cacheFile;

    // Bloc static exécuté une seule fois
    public static function init(): void
    {
        if (self::$cacheFile === null) {
            self::$cacheFile = AppConfig::getConst('ROOT_PATH_STORAGE_CACHE') . 'routesControllers.php';
        }
    }

    public static function exists(): bool
    {
        self::init(); // S'assure que $cacheFile est bien initialisé
        return is_file(self::$cacheFile);
    }

    public static function load(): array
    {
        self::init(); // S'assure que $cacheFile est bien initialisé
        return require self::$cacheFile;
    }

    public static function store(array $routes): void
    {
        self::init(); // S'assure que $cacheFile est bien initialisé
        file_put_contents(
            self::$cacheFile,
            '<?php return ' . var_export($routes, true) . ';'
        );
    }

    public static function clear(): void
    {
        if (self::exists()) {
            unlink(self::$cacheFile);
        }
    }
}
