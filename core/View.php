<?php
// core/View.php

namespace Core;

use Src\Modules\Shared\Interface\Http\Controllers\HeaderController;
use Config\AppConfig;
use Core\Support\DebugHelper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View
{
    /**
     * Instance de Twig, initialisée une seule fois
     */
    private static ?Environment $twig = null;

    /**
     * Initialise Twig une seule fois
     */
    private static function initTwig()
    {
        if (self::$twig !== null) {
            return;
        }

        $debug = AppConfig::getBool('APP_DEBUG');
        $loader = new FilesystemLoader();
        $cacheViews = self::getCacheRoutesViews();

        foreach ($cacheViews as $moduleName => $viewsPath) {
            $loader->addPath($viewsPath, $moduleName);
        }

        self::$twig = new Environment($loader, [
            'cache' => $debug ? false : AppConfig::getConst('ROOT_PATH_STORAGE_CACHE') . 'twig',
            'debug' => $debug,
            'auto_reload' => $debug,
        ]);

        // Ajoute les globals de configuration pour Twig
        foreach (AppConfig::getGlobalsForTwig() as $key => $value) {
            self::$twig->addGlobal($key, $value);
        }

        // Ajout du global 'app' (permet dans base d'obtenir la route)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $schemeAndHttpHost = $scheme . '://' . $host;
        self::$twig->addGlobal('app', (object)['request' => (object)['schemeAndHttpHost' => $schemeAndHttpHost]]);

        // ✅ Ajout de la fonction encore_asset dans Twig
        self::$twig->addFunction(new TwigFunction('encore_asset', function (string $asset): string {
            return self::resolveEncoreAsset($asset);
        }));

        // ✅ Ajout de la fonction encore_asset_optional dans Twig (retourne '' si absent)
        self::$twig->addFunction(new TwigFunction('encore_asset_optional', function (string $asset): string {
            return self::tryResolveEncoreAsset($asset) ?? '';
        }));
    }

    private static function getCacheRoutesViews(): array
    {
        $cacheViewsFile = AppConfig::getConst('ROOT_PATH_STORAGE_CACHE') . 'routesViews.php';
        $modulesPath = AppConfig::getConst('ROOT_PATH_SRC_MODULES');

        if (AppConfig::getBool('APP_DEBUG') && file_exists($cacheViewsFile))
            unlink($cacheViewsFile);
        if (!file_exists($cacheViewsFile))
            self::compileCacheRoute($modulesPath, $cacheViewsFile);
        return require $cacheViewsFile;
    }

    private static function compileCacheRoute($modulesPath, $cacheFile)
    {
        // Sinon on scanne
        $modules = [];

        foreach (scandir($modulesPath) as $moduleName) {
            if ($moduleName === '.' || $moduleName === '..')
                continue;

            $viewsPath = $modulesPath . $moduleName . "/UI/Views"; // Ex: "/var/www/html/src/Modules/" + "Product" + "/UI/Views" 

            if (is_dir($viewsPath)) {
                $modules[$moduleName] = $viewsPath;
            } else {
            }
        }

        // On génère le cache
        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export($modules, true) . ';',
            LOCK_EX
        );
    }

    /**
     * Résout un asset, lance une exception si absent (obligatoire)
     */
    private static function resolveEncoreAsset(string $asset): string
    {
        $resolved = self::tryResolveEncoreAsset($asset);

        if ($resolved === null) {
            throw new \RuntimeException("Asset $asset not found in manifest.json");
        }

        return $resolved;
    }

    /**
     * Résout un asset, retourne null si absent (optionnel)
     */
    private static function tryResolveEncoreAsset(string $asset): ?string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifestPath = AppConfig::getConst('ROOT_PATH_PUBLIC_BUILD') . 'manifest.json';
            if (!file_exists($manifestPath)) {
                return null;
            }
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        return $manifest['build/' . $asset] ?? null;
    }

    /**
     * Rend une vue avec Twig
     */
    public static function render(string $template, array $data = []): void
    {
        self::initTwig();

        // Injecter la session globale dans Twig
        self::$twig->addGlobal('session', $_SESSION);

        // Fusionne les données du contrôleur avec celles du header (s'il y a des données à traiter ex: barre de recherche)
        $mergedData = array_merge($data, self::getHeaderData());

        // Charger la vue Twig à l'aide du cacheViews correspondant au template appellé
        $result = self::$twig->render('@' . $template, $mergedData); # Cache views

        // Afficher la vue Twig
        echo $result;
    }

    /**
     * Récupère les données du HeaderController pour les inclure dans le rendu
     */
    private static function getHeaderData(): array
    {
        $headerController = new HeaderController();
        return $headerController->index();
    }
}
