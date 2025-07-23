<?php

namespace App\Core;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

class Controller
{
    /**
     * @var array $data Holds data to be passed to views.
     */
    protected array $data;

    /**
     * Controller constructor.
     * Initializes the controller with necessary data or configurations.
     */
    public function __construct()
    {
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
}
