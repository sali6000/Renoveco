<?php
// core/Router.php

namespace App\Core;

use App\Core\RouteContext;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Middleware\LoggerMiddleware;
use App\Core\Middleware\MaintenanceMiddleware;
use App\Core\Middleware\AccessControlMiddleware;

use App\Controllers\Utils\SitemapController;

class Router
{
    private $uri;
    private $controller = BASE_NAME_CONTROLLER_HOME;
    private $action = BASE_NAME_CONTROLLER_ACTION_DEFAULT;
    private $params = [];
    private $index;
    private $basename;

    /**
     * Middlewares to be applied for specific actions.
     * The keys are in the format 'Controller@action' or '*@*' for global middlewares.
     * The values are arrays of middleware class names to be executed.
     */
    private $middlewares = [
        'ProductController@create' => [AuthMiddleware::class],
        'AdminController@dashboard' => [AuthMiddleware::class, AdminMiddleware::class],
        '*@*' => [LoggerMiddleware::class, MaintenanceMiddleware::class, AccessControlMiddleware::class],
    ];

    /**
     * Router constructor.
     * Initializes the router with the given URI.
     * It sets the basename based on whether the URL contains "localhost".
     * If the URL is "/sitemap.xml", it redirects to the SitemapController's index method.
     *
     * @param string $uri The request URI.
     */
    public function __construct($uri)
    {
        // Définir le basename en fonction de l'URL
        $isLocalhost = strpos($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 'localhost') !== false;
        $this->basename = $isLocalhost ? BASE_NAME : '';

        // Vérification de la route sitemap.xml
        if ($_SERVER['REQUEST_URI'] === $this->basename . '/sitemap.xml') {
            $controller = new SitemapController();
            $controller->index();
            exit;
        }

        $this->index = $isLocalhost ? 0 : 0; # Pour les conteneurs Docker, l'index commence à 0. Sinon, il commence à 1 (WAMP car "/public" est ajouté à l'URL).
        $this->uri = $this->parseUri($uri);
        $this->setController();
        $this->setAction();
        RouteContext::set($this->controller, $this->action);
        $this->setParams();
    }

    /**
     * Parse the URI and return an array of segments.
     * It trims leading and trailing slashes and filters out empty segments.
     */
    private function parseUri($uri): array
    {
        $parsedUri = parse_url($uri, PHP_URL_PATH);
        $parsedUri = trim($parsedUri, '/');
        return array_filter(explode('/', $parsedUri), 'strlen');
    }

    /**
     * Set the controller based on the URI.
     * It checks for controllers in "Manage" and "Utils" subdirectories first,
     * then falls back to the root controllers.
     */
    private function setController(): void
    {
        // Vérifier si l'URI contient des segments
        if (isset($this->uri[$this->index])) {
            $first = ucfirst(filter_var($this->uri[$this->index], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $second = isset($this->uri[$this->index + 1]) ? ucfirst(filter_var($this->uri[$this->index + 1], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : null;

            // Sous-dossier "Manage"
            if ($first === 'Manage' && $second) {
                $controller = $second . 'Controller';
                if (class_exists('App\\Controllers\\Manage\\' . $controller)) {
                    $this->controller = 'Manage\\' . $controller;
                    $this->index++;
                    return;
                }
            }

            // Sous-dossier "Utils"
            if ($first === 'Utils' && $second) {
                $controller = $second . 'Controller';
                if (class_exists('App\\Controllers\\Utils\\' . $controller)) {
                    $this->controller = 'Utils\\' . $controller;
                    $this->index++;
                    return;
                }
            }

            // Cas standard (contrôleur à la racine)
            $controller = $first . 'Controller';
            if (class_exists('App\\Controllers\\' . $controller)) {
                $this->controller = $controller;
            } else {
                $this->handleError("Erreur 404: '{$controller}' n'est pas un controlleur accessible. (Voir Router.php)");
            }
        }
    }

    /**
     * Set the action based on the URI.
     * It checks if the action exists in the controller class.
     */
    private function setAction(): void
    {
        if (isset($this->uri[$this->index + 1])) {
            $action = filter_var($this->uri[$this->index + 1], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (method_exists('App\\Controllers\\' . $this->controller, $action)) {
                $this->action = $action;
            }
        }
    }

    /**
     * Set the parameters based on the remaining segments in the URI.
     * It sanitizes each parameter to prevent XSS attacks.
     */
    private function setParams(): void
    {
        if (isset($this->uri[$this->index + 2])) {
            $this->params = array_slice($this->uri, $this->index + 2);
            $this->params = array_map(function ($param) {
                return filter_var($param, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }, $this->params);
        }
    }

    public function route(): void
    {
        // 1. Vérification du contrôleur
        if (empty($this->controller)) {
            $this->handleError("Erreur 404: Aucun contrôleur spécifié dans l'URI.");
            return;
        }

        $controllerClass = 'App\\Controllers\\' . $this->controller;

        if (!class_exists($controllerClass)) {
            $this->handleError("Erreur 404: Contrôleur '{$this->controller}' introuvable.");
            return;
        }

        // 2. Vérification de l'action
        if (!method_exists($controllerClass, $this->action)) {
            $this->handleError("Erreur 404: Action '{$this->action}' introuvable dans '{$this->controller}'.");
            return;
        }

        try {
            // 3. Mise à jour du contexte de la route (POUR TOUS les middlewares ou logs)
            \App\Core\RouteContext::set($this->controller, $this->action);

            // 4. Exécution des middlewares selon différents scopes
            $keysToCheck = [
                RouteContext::get(),                // ex: ProductController@detail
                $this->controller . '@*',          // ex: ProductController@*
                '*@*'                               // global middlewares
            ];

            foreach ($keysToCheck as $key) {
                if (!$this->handleMiddlewares($key)) {
                    return;
                }
            }

            // 5. Instanciation du contrôleur + appel de la méthode avec les paramètres dynamiques
            $controllerObject = ControllerFactory::create($controllerClass);
            call_user_func_array([$controllerObject, $this->action], $this->params);
        } catch (\Throwable $e) {
            $this->handleError("Erreur 500: " . $e->getMessage());
        }
    }


    /**
     * Handle middlewares for the given key.
     * It checks if the middlewares exist and executes them.
     * If a middleware returns false, it stops further processing.
     *
     * @param string $key The key to check for middlewares.
     * @return bool Returns true if all middlewares pass, false otherwise.
     */
    private function handleMiddlewares(string $key): bool
    {
        // Si aucune middleware n'est associée à la clé, on considère que c'est un succès
        if (!isset($this->middlewares[$key])) return true;

        foreach ($this->middlewares[$key] as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new \Exception("Le middleware '{$middlewareClass}' n'existe pas.");
            }

            $middleware = new $middlewareClass();

            if (!$middleware instanceof \App\Core\Middleware) {
                throw new \Exception("Le middleware '{$middlewareClass}' doit étendre la classe Middleware.");
            }

            if (!$middleware->handle()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Handle errors by displaying a message.
     * This method is used to display error messages when routing fails.
     *
     * @param string $message The error message to display.
     */
    private function handleError($message): void
    {
        echo "<h1>Erreur 404</h1><p>{$message}</p>";
        exit;
    }
}
