<?php

namespace Core;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\View;
use Core\Logger\AccessLogger;
use Core\Support\DebugHelper;

abstract class Controller
{
    /**
     * @var array $data Holds data to be passed to views.
     */
    protected array $data;
    protected string $viewBase;

    /**
     * Controller constructor.
     * Initializes the controller with necessary data or configurations.
     */
    public function __construct()
    {
        if (!property_exists(static::class, 'VIEW') && !defined('static::VIEW')) {
            throw new \LogicException(static::class . ' doit définir la constante VIEW.');
        }
        $this->viewBase = constant(static::class . '::VIEW');

        // Initialisation des données ou autres configurations si nécessaire
        $this->data = [];
    }

    /**
     * Renders a view with the provided data.
     *
     * @param string $view The view file to render.
     * @param array $data The data to pass to the view.
     */
    protected function view(string $view, array $data = [])
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

    protected function handleException(\Throwable $e, string $context = 'Erreur', string $view = 'error/500')
    {
        $errorId = uniqid('err_', true);
        $errorType = get_class($e);

        AccessLogger::log("[$errorId] ❌ $context → $errorType : " . $e, AccessLogger::LEVEL_ERROR);

        $message = ($_ENV['APP_ENV'] === 'dev')
            ? $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>'
            : "Une erreur est survenue voir les logs (Code : $errorId)";

        $this->view($view, ['message' => $message]);
    }

    protected function render(string $action = 'index'): void
    {
        // $this->viewBase contient la VIEW définit dans le controller Ex: "Product"
        // Ex: $page devient donc "Product"/detail" si action contient 'detail', sinon "Product/index par défaut"
        $assets = strtolower($action == 'index' ? $this->viewBase : $this->viewBase . '-' . $action); // product-detail
        $view = $this->viewBase . '/' . $action; // Product/detail
        $this->set('current_page', $assets);
        $this->view($view, $this->data);
    }

    protected function setCache(int $seconds = 3600): void
    {
        header("Cache-Control: public, max-age=$seconds");
        header("Pragma: cache");
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + $seconds) . " GMT");
    }
}
