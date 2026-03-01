<?php

namespace Core\Routing;

class RouteCache
{

    private const CACHE_FILE = __DIR__ . '/../../storage/cache/routesControllers.php';

    public static function exists(): bool
    {
        return is_file(self::CACHE_FILE);
    }

    public static function load(): array
    {
        return require self::CACHE_FILE;
    }

    public static function store(array $routes): void
    {
        file_put_contents(
            self::CACHE_FILE,
            '<?php return ' . var_export($routes, true) . ';'
        );
    }

    public static function clear(): void
    {
        if (self::exists()) {
            unlink(self::CACHE_FILE);
        }
    }
}
