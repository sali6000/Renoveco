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
            // Désormais, on ne lance l'exception que si le fichier n'existe VRAIMENT pas sur le disque
            throw new \RuntimeException("Asset $asset non trouvé dans le manifest ou sur le disque.");
        }

        return $resolved;
    }

    private static function tryResolveEncoreAsset(string $asset): ?string
    {
        static $manifest = null;
        $buildPath = AppConfig::getConst('ROOT_PATH_PUBLIC_BUILD');

        // 1. Chargement du Manifest de Vite
        if ($manifest === null) {
            $manifestPath = $buildPath . '.vite/manifest.json';
            if (!file_exists($manifestPath)) {
                $manifestPath = $buildPath . 'manifest.json';
            }
            $manifest = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true)
                : [];
        }

        // 2. Recherche dans le manifest
        foreach ($manifest as $key => $data) {
            // Par nom court : 'global.css', 'home-index.css', 'app.js'
            if (isset($data['names']) && in_array($asset, $data['names'])) {
                return '/build/' . $data['file'];
            }
            // Par fin de clé source : 'src/assets/scss/_main.scss'
            if (str_ends_with($key, $asset)) {
                return '/build/' . $data['file'];
            }
        }

        // 3. Fallback fichiers statiques copiés (img, webm...)
        $staticPath = $buildPath . $asset;
        if (file_exists($staticPath)) {
            return '/build/' . $asset;
        }

        return null;
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

        // Capitaliser le premier segment pour matcher les namespaces (ex. : 'services/...' -> 'Services/...')
        $parts = explode('/', $template, 2);
        $parts[0] = ucfirst($parts[0]);  // Majuscule sur le premier mot
        $capitalizedTemplate = implode('/', $parts);

        // Charger la vue Twig
        $result = self::$twig->render('@' . $capitalizedTemplate, $mergedData);

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
