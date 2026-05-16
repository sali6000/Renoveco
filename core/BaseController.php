<?php

namespace Core;

use Config\AppConfig;
use Core\View;
use Core\Logger\AccessLogger;
// =============================================================================
// BaseController.php — Conversion viewPath → clé CSS (robuste, long terme)
// =============================================================================
//
// RÈGLE DE CORRESPONDANCE (identique côté Vite) :
//   viewPath (Twig)                          →  clé SCSS / current_page
//   ─────────────────────────────────────────────────────────────────
//   Home/index.twig                          →  home-index
//   Product/detail.twig                      →  product-detail
//   Services/index.twig                      →  services-index
//   Services/panneaux-photovoltaiques.twig   →  services-panneaux-photovoltaiques
//   Services/chassis-et-fenetres.twig        →  services-chassis-et-fenetres
//
// STRUCTURE SCSS source correspondante :
//   src/assets/scss/pages/
//   └── services/
//       ├── index.scss
//       ├── panneaux-photovoltaiques.scss
//       └── chassis-et-fenetres.scss
//
// =============================================================================

abstract class BaseController
{
    protected function render(string $viewPath, array $datas = []): void
    {
        $datas['current_page']      = $this->convertViewPathToAssetKey($viewPath);
        $datas['current_page_path'] = $this->convertViewPathToScssPath($viewPath);
        View::render($viewPath, $datas);
    }
    protected function convertViewPathToAssetKey(string $viewPath): string
    {
        return implode('-', $this->getViewSegments($viewPath));
    }

    protected function convertViewPathToScssPath(string $viewPath): string
    {
        return implode('/', $this->getViewSegments($viewPath));
    }

    private function getViewSegments(string $viewPath): array
    {
        $normalized = str_replace('\\', '/', $viewPath);
        $segments   = explode('/', $normalized);
        $segments[count($segments) - 1] = pathinfo(end($segments), PATHINFO_FILENAME);
        return array_values(array_filter(array_map('strtolower', $segments)));
    }

    protected static function validateSlug(string $slug): void
    {
        if (!preg_match('/^[a-zA-Z0-9-]{1,50}$/', $slug)) {
            throw new \Exception("Slug invalide : seuls les lettres, chiffres et tirets sont autorisés (max 50 caractères).");
        }
    }

    protected static function normalizeViewSlug(string $slug): string
    {
        return strtolower($slug);
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
