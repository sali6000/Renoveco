<?php
// core/View.php

namespace Core;

use App\Modules\Shared\Interface\Http\Controllers\HeaderController;
use Config\AppConfig;
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
            'cache' => $debug ? false : AppConfig::getConst('LOCAL_PATH_APP_CACHE_TWIG'),
            'debug' => $debug,
            'auto_reload' => $debug,
        ]);

        // Ajoute les globals de configuration pour Twig
        foreach (AppConfig::getGlobalsForTwig() as $key => $value) {
            self::$twig->addGlobal($key, $value);
        }

        // ✅ Ajout de la fonction encore_asset dans Twig
        self::$twig->addFunction(new TwigFunction('encore_asset', function (string $asset): string {
            return self::resolveEncoreAsset($asset);
        }));
    }

    private static function compileCacheRoute($modulesPath, $cacheFile)
    {

        // Sinon on scanne
        $modules = [];

        foreach (scandir($modulesPath) as $moduleName) {
            if ($moduleName === '.' || $moduleName === '..')
                continue;

            $viewsPath = $modulesPath . $moduleName; // Ex: "/var/www/html/app/modules/Product/" 

            if (is_dir($viewsPath))
                $modules[$moduleName] = $viewsPath;
        }

        // On génère le cache
        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export($modules, true) . ';',
            LOCK_EX
        );
    }

    private static function getCacheRoutesViews(): array
    {
        $cacheViewsFile = AppConfig::getConst('LOCAL_PATH_STORAGE_CACHES') . 'routesViews.php';
        $modulesPath = AppConfig::getConst('LOCAL_PATH_APP_MODULES');

        if (AppConfig::getBool('APP_DEBUG') && file_exists($cacheViewsFile))
            unlink($cacheViewsFile);
        if (!file_exists($cacheViewsFile))
            self::compileCacheRoute($modulesPath, $cacheViewsFile);
        return require $cacheViewsFile;
    }

    /**
     * Résout le chemin d'un asset depuis manifest.json
     */
    private static function resolveEncoreAsset(string $asset): string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifestPath = AppConfig::getConst('PUBLIC_PATH_BUILD') . 'manifest.json';
            if (!file_exists($manifestPath)) {
                throw new \RuntimeException("Manifest not found: $manifestPath");
            }
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (!isset($manifest['build/' . $asset])) {
            throw new \RuntimeException("Asset $asset not found in manifest.json, if it's SCSS, verify webpack.config.js Entries");
        }

        return $manifest['build/' . $asset];
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
        $result = self::$twig->render('@' . $template . '.twig', $mergedData);
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
