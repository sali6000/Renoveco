<?php

namespace Core\Routing;

use Config\AppConfig;
use Core\Logger\AccessLogger;
use Core\BaseController;
use ReflectionClass;
use Core\Routing\Attribute\Route;
use Core\Routing\Exception\RoutingException;
use Core\Support\DebugHelper;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionMethod;

class RouteCompiler
{
    // Mise en cache des controllers et attributs associés
    public static function compile(): array
    {
        $routes = [];

        // Vérifier le cache persistant des routes
        $cacheFile = self::getCacheFilePath();

        try {
            if (self::isCacheValid($cacheFile)) {
                return self::loadCache($cacheFile);
            }

            DebugHelper::verboseServer('Cache des routesController non trouvé ou invalide. Début du scan des routes...');

            // Ex: '*/Interface/Http/Controllers/*Controller.php', ...
            foreach (self::getControllersPath() as $controllerPath) {

                // Ex: 'Src\\...Controller <= '*/Interface/Http/Controllers/*.php'
                $class = self::convertControllerPathToNamespace($controllerPath);

                foreach (self::getRoutesFromClass($class) as $method => $methodRoutes) {
                    /**
                     * Exemple de cacheRoute:
                     * [routes] => Array
                     *      [GET] => Array
                     *              [0] => Array
                     *                  [class] => Src\Modules\Home\Interface\Http\Controllers\HomeIndexController
                     *                  [controller] => HomeIndexController
                     *                  [action] => index
                     *                  [pattern] => #^/$#
                     *                  [params] => Array
                     */
                    if (!isset($routes[$method])) {
                        $routes[$method] = [];
                    }

                    $routes[$method] = array_merge(
                        $routes[$method],
                        $methodRoutes
                    );
                }
            }

            // Générer et écrire le cache (hash des fichiers contrôleurs)
            $controllers = self::getControllersPath();
            $hash = self::computeFilesHash($controllers);
            self::writeCache($cacheFile, $routes, $hash);
        } catch (RoutingException $ex) {
            DebugHelper::verboseServer("Erreur dans compile du cache");
            DebugHelper::verboseServer($ex);
        }
        return ['routes' => $routes];
    }


    // Return:
    // ['/var/www/...src/Modules/Products/....DetailController.php',
    // '/var/www/...src/Modules/Products/....ListController.php',
    // '/var/www/...src/Modules/Users/....Controller.php', ...]
    private static function getControllersPath(): array
    {
        $basePath = AppConfig::getConst('ROOT_PATH_SRC_MODULES');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        $controllers = [];

        foreach ($iterator as $file) {
            if (
                $file->isFile() &&
                str_contains($file->getPathname(), 'Interface/Http/Controllers/') &&
                $file->getExtension() === 'php'
            ) {
                $controllers[] = $file->getPathname();
            }
        }

        // Retourne TOUTES les classes correspondants
        return $controllers;
    }

    private static function getCacheFilePath(): string
    {
        return realpath(__DIR__ . '/../../storage') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'routesControllers.php';
    }

    private static function computeFilesHash(array $files): string
    {
        $parts = [];
        foreach ($files as $f) {
            if (file_exists($f)) {
                $parts[] = $f . ':' . filemtime($f);
            }
        }
        return md5(implode('|', $parts));
    }

    private static function isCacheValid(string $cacheFile): bool
    {
        if (AppConfig::getBool('APP_DEBUG')) return false;

        if (!file_exists($cacheFile)) {
            return false;
        }
        $data = @include $cacheFile;
        if (!is_array($data) || !isset($data['hash']) || !isset($data['routes'])) {
            return false;
        }
        $controllers = self::getControllersPath();
        $currentHash = self::computeFilesHash($controllers);
        return $currentHash === $data['hash'];
    }

    private static function loadCache(string $cacheFile): array
    {
        $data = @include $cacheFile;
        return is_array($data) && isset($data['routes']) ? $data['routes'] : [];
    }

    private static function writeCache(string $cacheFile, array $routes, string $hash): void
    {
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $export = var_export(['routes' => $routes, 'hash' => $hash], true);
        $content = "<?php\nreturn " . $export . ";\n";
        @file_put_contents($cacheFile, $content, LOCK_EX);
    }

    // from /var/www/html/...src/Modules/Products/....Controller.php 
    // to Src\Modules\Product\....Controller
    private static function convertControllerPathToNamespace(string $file): string
    {
        // Normaliser le chemin
        // Supprimer .php
        // Et valider le classPath en tant que Class
        $normalized = str_replace('\\', '/', $file);
        $relative = str_replace(AppConfig::getConst('ROOT_PATH_SRC_MODULES'), '', $normalized);
        $relative = str_replace('.php', '', $relative);
        $classPath = 'Src\\Modules\\' . str_replace('/', '\\', ltrim($relative, '/'));


        if (!class_exists($classPath)) {
            if (AppConfig::getBool('APP_DEBUG')) {
                DebugHelper::verboseServer('X La class ' . $classPath . ' n\'existe pas. Le souci vient du namespace ou bien du chemin.');
            }
            AccessLogger::log("⚠️ Warning: Class not found: $classPath", AccessLogger::LEVEL_WARNING);
            throw new Exception('Erreur dans la validation de la classe');
        }

        if (!is_subclass_of($classPath, BaseController::class)) {
            if (AppConfig::getBool('APP_DEBUG')) {
                DebugHelper::verboseServer('X La classe n\'est pas un BaseController : ' . $classPath);
            }
            AccessLogger::log("🔒 Security: Rejected non-BaseController: $classPath", AccessLogger::LEVEL_WARNING);
            throw new Exception('Erreur dans la validation de la classe');
        }
        return $classPath;
    }

    private static function getRoutesFromClass(string $class): array
    {

        // ROUTE MODULE: 
        // Ex: 'Src\Modules\Product\...' => 'Product'
        RouteContext::getInstance()->setModule(self::convertClassToModuleName($class));

        // ROUTE CLASS:
        // Ex: #[Route('/product')] => [path => '/product', methods[0] => 'GET']
        $classReflection = new ReflectionClass($class);
        $classRoute = self::getRouteAttrFrom($classReflection);

        //ROUTES METHODES:
        // Ex: #[Route('detail/{slug}', methods: ['GET'])] => [path => 'detail/{slug}', methods[0] => 'GET']
        $routes = [];

        // Si la class a une route
        if (!empty($classRoute)) {

            // 3. Parcourir les méthodes publics ayant une route
            foreach ($classReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

                // 1. Ignorer le constructeur
                if ($method->isConstructor()) {
                    continue;
                }

                // 2. Ignorer les méthodes héritées
                if ($method->getDeclaringClass()->getName() !== $class) {
                    continue;
                }

                // 3. Récupérer l'attribut Route de la méthode
                $methodRoute = self::getRouteAttrFrom($method);

                // 4. Ignorer si aucune route n'a été trouvée
                if (!$methodRoute) {
                    continue;
                }

                // 5. Assembler la Route 
                // /product + /detail/{slug} => '/product/detail/{slug}'
                $fullPath = self::mergePaths(
                    $classRoute?->path, // (2)
                    $methodRoute->path
                );

                // 6. Enregistrer les informations liés à la méthode
                foreach ($methodRoute->methods as $httpMethod) {
                    /**
                     * Exemple de routeCache:
                     * [GET] => Array (
                     *      [0] => Array (
                     *          [class] => Src\Modules\Home\Interface\Http\Controllers\HomeIndexController
                     *          [controller] => HomeIndexController
                     *          [action] => index
                     *          [pattern] => #^/$#
                     *          [params] => Array
                     *      (
                     * )
                     */
                    $routes[$httpMethod][] = [
                        'class' => $class,
                        'controller' => $classReflection->getShortName(),
                        'action'     => $method->getName(),
                        'pattern'    => self::convertUriToRgx($fullPath),
                        'params'     => self::convertParamsToArray($fullPath),
                    ];
                }
            }
        }
        return $routes;
    }

    // Récupérer le nom du module depuis le namespace
    private static function convertClassToModuleName(string $classPath): string
    {
        // 'Product' <= 'Src\Modules\Product\Interface\Http\Controllers\...'
        preg_match('#Modules\\\\([^\\\\]+)#', $classPath, $m);
        return $m[1] ?? 'App';
    }

    // Récupérer les valeurs se trouvant dans l'attribut Route
    private static function getRouteAttrFrom($ref): ?Route
    {
        // '/product' <= #[Route('/product')]
        $attrs = $ref->getAttributes(Route::class); // Récupération des routes
        return $attrs ? $attrs[0]->newInstance() : null; // Si il y a une route, instancier l'objet
    }

    // Convertir en regex les paramètres de la route
    private static function convertUriToRgx(string $path): string
    {
        // Build regex by quoting static parts and replacing {param} with named groups
        $parts = preg_split('#\{(\w+)\}#', $path, -1, PREG_SPLIT_DELIM_CAPTURE);
        $regex = '';
        for ($i = 0; $i < count($parts); $i++) {
            if ($i % 2 === 0) {
                // static segment
                $regex .= preg_quote($parts[$i], '#');
            } else {
                // captured param name
                $name = $parts[$i];
                $regex .= '(?P<' . $name . '>[^/]+)';
            }
        }

        return '#^' . $regex . '$#';
    }

    // Récupérer les paramètres
    private static function convertParamsToArray(string $path): array
    {
        // ['slug'] <= /product/detail/{slug}
        preg_match_all('#\{(\w+)\}#', $path, $m);
        return $m[1];
    }

    // /product/detail/{slug} <= /product + /detail/{slug}
    private static function mergePaths(?string $a, string $b): string
    {
        if (empty($a)) {
            return '/' . trim($b, '/');
        }
        return '/' . trim($a . '/' . $b, '/');
    }
}
