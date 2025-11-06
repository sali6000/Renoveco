<?php
// core/View.php

namespace Core;

use App\Modules\Shared\Controller\HeaderController;
use Config\AppConfig;
use Core\ControllerFactory;
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
        if (self::$twig === null) {

            $debug = AppConfig::getBool('APP_DEBUG');
            $loader = new FilesystemLoader();
            $modulesPath = AppConfig::getPath('APP_PATH_LOCAL_APP_MODULES');

            // ✅ Shared : explicitement déclaré
            $loader->addPath($modulesPath . "Shared/Views", "Shared");
            // Permet d'accéder à:
            //  Un layout       => {% extends "@Shared/layout/base.twig" %}
            //  Un component    => {% include '@Shared/components/header.twig' ignore missing %}

            // ✅ Modules : scan automatique
            foreach (scandir($modulesPath) as $moduleName) {
                if ($moduleName === '.' || $moduleName === '..' || $moduleName === 'Shared') continue;

                $viewsPath = $modulesPath . $moduleName . "/Views"; // Ex: "/var/www/html/app/modules/Product/Views" 
                if (is_dir($viewsPath)) $loader->addPath($viewsPath, $moduleName);
                //------------------------------------------------------------------------
                //  Exemple d'utilisation (Ex: ProductController::List):
                //------------------------------------------------------------------------
                //  protected const VIEW = 'Product';
                //  List() { $this->render(__FUNCTION__); } => $this->view("@Product/List");
                //  Résultat: echo "/var/www/html/app/modules/Product/Views/list.twig"
                //------------------------------------------------------------------------
                DebugHelper::verboseHtml("View.php => Module:", [$viewsPath], false);
            }

            self::$twig = new Environment($loader, [
                'cache' => $debug ? false : AppConfig::getPath('APP_PATH_LOCAL_APP_CACHE_TWIG'),
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
    }

    /**
     * Résout le chemin d'un asset depuis manifest.json
     */
    private static function resolveEncoreAsset(string $asset): string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifestPath = AppConfig::getPath('APP_PATH_LOCAL_PUBLIC_BUILD') . 'manifest.json';
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
    public static function render(string $template, array $data = [])
    {
        self::initTwig();

        // Injecter la session globale dans Twig
        self::$twig->addGlobal('session', $_SESSION);

        // Fusionne les données du contrôleur avec celles du header (s'il y a des données à traiter ex: barre de recherche)
        $mergedData = array_merge($data, self::getHeaderData());

        echo self::$twig->render('@' . $template . '.twig', $mergedData);
    }

    /**
     * Permet d'inclure un composant Twig depuis un autre endroit
     */
    public static function renderPartial(string $template, array $data = [])
    {
        self::initTwig();
        echo self::$twig->render('@' . $template . '.twig', $data);
    }

    /**
     * Récupère les données du HeaderController pour les inclure dans le rendu
     */
    private static function getHeaderData(): array
    {
        $headerController = ControllerFactory::create(HeaderController::class);
        return $headerController->index();
    }
}
