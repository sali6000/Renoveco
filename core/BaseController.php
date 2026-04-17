<?php
// Hello world

namespace Core;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\AppConfig;
use Core\View;
use Core\Logger\AccessLogger;

abstract class BaseController
{
    /**
     * Retourner la vue avec que les données associées (current_page + datas).
     * Exemple avec:
     * - render("Product/detail.twig", ['name' => 'Henri]) 
     * 
     * 
     * Retourne la vue Product/Ui/Views/detail.twig avec 'Henri' et 'product-detail' acessibles
     * 
     * Le current_page sert d'identifiant pour:
     * - Déterminer quel page scss appeller (depuis Webpack.config.js),
     * - Appeller la page scss correspondante (depuis base.twig),
     * - Appeller la page js correspondante (depuis app.js),
     * - Établir des conditions (dans les vues et les assets).
     */
    protected function render(string $viewPath, array $datas = []): void
    {
        $assetsPath = $this->convertViewPathToScssPath($viewPath); # "product-detail" <= "Product/detail.twig"
        $datas['current_page'] = $assetsPath; # Pour Webpack Encore
        View::render($viewPath, $datas);
    }

    protected function convertViewPathToScssPath(string $view): string
    {
        // Séparer le chemin en parties (module et vue)
        $parts = preg_split("#[\\/]+#", $view); // ['Product', 'detail.twig']

        // Prendre la première partie (module)
        $module = $parts[0]; // 'Product'

        // Prendre la deuxieme partie (vue) sans l'extension .twig
        $file = pathinfo($parts[count($parts) - 1], PATHINFO_FILENAME); // 'detail'

        return strtolower($module . '-' . $file); // "product-detail"
    }

    protected function redirect(string $url, int $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    protected function handleException(\Throwable $e, string $context = 'Erreur', string $view = 'Error/UI/Views/500')
    {
        $errorId = uniqid('err_', true);
        $errorType = get_class($e);

        AccessLogger::log("[$errorId] ❌ $context → $errorType : " . $e, AccessLogger::LEVEL_ERROR);

        $message = (AppConfig::getEnv('APP_ENV') === 'dev')
            ? $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>'
            : "(Voir les logs liés au code : $errorId)";
        $this->render($view, ['message' => $message]);
    }

    protected function setCache(int $seconds = 3600): void
    {
        header("Cache-Control: public, max-age=$seconds");
        header("Pragma: cache");
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + $seconds) . " GMT");
    }

    protected function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'] ??= [];
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    protected function hasFlash(string $key): bool
    {
        return isset($_SESSION['flash'][$key]);
    }
}
