<?php

namespace Core;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Config\AppConfig;
use Core\View;
use Core\Logger\AccessLogger;

abstract class BaseController
{
    private string $views = '';
    private ?string $module = null;
    protected array $data; // Ex: $categories[]

    /**
     * Controller constructor.
     *
     * Initialise le contrôleur en définissant :
     *   - Le chemin complet du module (ex : "Admin/Category")
     *   - Le chemin interne des vues du module (ex : "Manage")
     *
     * Ces paramètres permettent au contrôleur parent de :
     *   - Localiser automatiquement les dossiers Modules/<Module>/Views
     *   - Résoudre les chemins de templates Twig
     *   - Déduire les noms d’assets associés (Webpack Encore)
     *
     * ---------------------------------------------------------------------
     * @param string $modulePath
     *   Nom complet du module. (Permet de définir la route entre Modules/.../Views)
     *   Format attendu :
     *     - "Dossier" ou "Dossier/SousDossier"
     * 
     *   Exemples :
     *     - "Admin/Dashboard"  → Modules/Admin/Dashboard/Views/...
     *     - "Admin/Category"   → Modules/Admin/Category/Views/...
     *     - "Home"             → Modules/Home/Views/...
     *
     * @param string $viewsPath
     *   Nom complet de la vue. (Permet de définir la route entre Views/.../action.twig)
     *   Format attendu :
     *     - "Dossier" ou "Dossier/SousDossier"
     * 
     *   Exemples :
     *     - "Manage"                  → Views/Manage/index.twig
     *     - "Manage/Sub"              → Views/Manage/Sub/list.twig
     *     - "" (vide)                 → Views/index.twig (contrôleur sans sous-dossier)
     *
     * ---------------------------------------------------------------------
     * @throws \LogicException Si aucun module n’est défini.
     */
    public function __construct(string $module, ?string $views = null)
    {
        // Vérification MODULE obligatoire dans l'enfant
        if ($module === null) {
            throw new \LogicException("Le constructeur n'a pas défini de module.");
        }

        $this->module = $module;

        // 🟦 Si viewsPath n'est pas fourni → on garde la valeur par défaut définie dans la propriété
        if ($views !== null) {
            $this->views = $views;
        }

        $this->data = [];
    }

    /**
     * Renders a view with the provided data.
     *
     * @param string $view The view file to render.
     * @param array $data The data to pass to the view.
     */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    /**
     * Redirects to a specified URL.
     *
     * @param string $url The URL to redirect to.
     * @param int $statusCode The HTTP status code for the redirect (default is 302).
     */
    protected function redirect(string $url, int $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Sets a value in the controller's data array.
     *
     * @param string $key The key for the data.
     * @param mixed $value The value to set.
     */
    protected function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Gets a value from the controller's data array.
     *
     * @param string $key The key for the data.
     * @return mixed The value associated with the key, or null if not set.
     */
    protected function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    protected function handleException(\Throwable $e, string $context = 'Erreur', string $view = 'Error/UI/Views/500')
    {
        $errorId = uniqid('err_', true);
        $errorType = get_class($e);

        AccessLogger::log("[$errorId] ❌ $context → $errorType : " . $e, AccessLogger::LEVEL_ERROR);

        $message = (AppConfig::getEnv('APP_ENV') === 'dev')
            ? $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>'
            : "(Voir les logs liés au code : $errorId)";
        $this->view($view, ['message' => $message]);
    }

    protected function render(string $action = 'index'): void
    {
        // ASSETS
        $assets = strtolower($this->getAssetsFromViewsPath() . '-' . $action);
        $this->set('current_page', $assets);

        // VUE
        $viewPart = $this->views ? $this->views . '/' : '';
        $viewPath = $this->module . '/UI/Views/' . $viewPart . $action;

        // Rendre la vue TWIG avec le SCSS/JS associé.
        $this->view($viewPath, $this->data);
    }

    protected function getAssetsFromViewsPath(): string
    {
        // Concat MODULE + VIEWS
        $path = $this->module . '/' . $this->views;

        // Remplacer les "/" par "-" et mettre en minuscules
        $slug = str_replace('/', '-', strtolower($path));

        // Nettoyage : supprime les tirets consécutifs éventuels si VIEWS est vide
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
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
