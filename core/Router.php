<?php
// core/Router.php

namespace Core;

use App\Controllers\Utilities\SitemapController;
use Config\AppConfig;
use Core\ControllerFactory;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\AdminMiddleware;
use Core\Middleware\LoggerMiddleware;
use Core\Middleware\MaintenanceMiddleware;
use Core\Middleware\AccessControlMiddleware;
use Core\Middleware\SecurityHeaderMiddleware;
use Core\RouteContext;
use Core\Support\DebugHelper;

class Router
{
    private $uri;
    private $module;
    private $controller;
    private $action;
    private $params = [];
    private $index;
    private $basename;
    private $showDebug = false;

    /**
     * Middlewares to be applied for specific actions.
     * The keys are in the format 'Controller@action' or '*@*' for global middlewares.
     * The values are arrays of middleware class names to be executed.
     */
    private $middlewares = [
        '*@*' => [LoggerMiddleware::class, MaintenanceMiddleware::class, AccessControlMiddleware::class, SecurityHeaderMiddleware::class], // accessible librement sauf admin/login
        'ProductController@create' => [AuthMiddleware::class], // nécessite juste d’être connecté
        'OrderController@*' => [AuthMiddleware::class],
        'AdminController@*' => [AuthMiddleware::class, AdminMiddleware::class], // nécessite d’être admin sur toutes ses méthodes
        'AdminController@dashboard' => [AuthMiddleware::class, AdminMiddleware::class]
    ];

    /**
     * Router constructor.
     * Initializes the router with the given URI.
     * It sets the basename based on whether the URL contains "localhost".
     * If the URL is "/sitemap.xml", it redirects to the SitemapController's index method.
     *
     * @param string $uri The request URI.
     */
    public function __construct($uri) // /product/list
    {
        // Définir le basename en fonction de l'URL et établis les configurations par défault
        // isLocalhost = true si 'localhost' est contenu la requête URI "https://localhost..."
        $isLocalhost = strpos($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 'localhost') !== false;
        $this->basename = $isLocalhost ? AppConfig::getPath('APP_PARAM_NAME') : ''; // /MonSite
        $this->controller = AppConfig::getPath('APP_PARAM_CONTROLLER_DEFAULT'); // HomeController
        $this->action = AppConfig::getPath('APP_PARAM_ACTION_DEFAULT'); // index

        // Vérification de la route sitemap.xml
        if ($_SERVER['REQUEST_URI'] === $this->basename . '/sitemap.xml') {
            $controller = new SitemapController();
            $controller->index();
            exit;
        }

        $this->index = $isLocalhost ? 0 : 0; # Pour les conteneurs Docker, l'index commence à 0. Sinon, il commence à 1 (WAMP car "/public" est ajouté à l'URL).
        $this->uri = $this->parseUri($uri); // [0] => product [1] => list
        $this->setController();
        $this->setAction();
        $this->setParams();
        RouteContext::set($this->controller, $this->action);

        DebugHelper::verboseHtml('Router après traitement du constructeur: ', [
            '$_SERVER[\'HTTP_HOST\']' => $_SERVER['HTTP_HOST'],
            '$_SERVER[\'REQUEST_URI\']' => $_SERVER['REQUEST_URI'],
            '$isLocalHost' => $isLocalhost,
            '$basename' => $this->basename,
            '$this->index' => $this->index,
            '$this->uri' => $this->uri,
            '$this->controller' => $this->controller,
            '$this->action' => $this->action,
            '$this->params' => $this->params
        ], $this->showDebug);
    }

    private function parseUri($uri): array
    {
        $parsedUri = parse_url($uri, PHP_URL_PATH); // /product/list
        $parsedUri = trim($parsedUri, '/'); // product/list

        // si la chaîne est vide (donc racine "/"), on met "home" par défaut
        if ($parsedUri === '') {
            $parsedUri = 'home';
        }
        return array_filter(explode('/', $parsedUri), 'strlen'); // [0] => product [1] => list
    }

    private function setController(): void
    {
        // Si $this->uri[0] n'est pas vide (alors https://localhost + Product/ + List/)
        if (isset($this->uri[$this->index])) {
            $this->module = ucfirst(filter_var($this->uri[$this->index], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            // $this->module = Product
            $second = isset($this->uri[$this->index + 1]) ? ucfirst(filter_var($this->uri[$this->index + 1], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : null;
            // $second = List


            // Sous-dossier "Manage"
            if ($this->module === 'Manage' && $second) {
                $controller = $second . 'Controller';
                if (class_exists('App\\Controllers\\Manage\\' . $controller)) {
                    $this->controller = 'Manage\\' . $controller;
                    $this->index++;
                    return;
                }
            }

            // Sous-dossier "Utils"
            if ($this->module === 'Utils' && $second) {
                $controller = $second . 'Controller';
                if (class_exists('App\\Controllers\\Utils\\' . $controller)) {
                    $this->controller = 'Utils\\' . $controller;
                    $this->index++;
                    return;
                }
            }

            // Cas standard (contrôleur à la racine)
            $controller = $this->module . 'Controller';
            if (class_exists('App\\Modules\\' . $this->module . '\\Controller\\' . $controller)) {
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
            if (method_exists('App\\Modules\\' . $this->module . '\\Controller\\' . $this->controller, $action)) {
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

        $controllerClass = 'App\\Modules\\' . $this->module . '\\Controller\\' . $this->controller;

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
            RouteContext::set($this->controller, $this->action);

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

            // 5. Instanciation du contrôleur via le ControllerFactory
            $controllerObject = ControllerFactory::create($controllerClass);

            // 6. Exécution de l'action du contrôleur avec les paramètres
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

        // Vérification de l'existence des middlewares et de leur type
        foreach ($this->middlewares[$key] as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new \Exception("Le middleware '{$middlewareClass}' n'existe pas.");
            }

            // Instanciation du middleware
            $middleware = new $middlewareClass();

            // Vérification que l'instance est bien un Middleware
            if (!$middleware instanceof Middleware) {
                throw new \Exception("Le middleware '{$middlewareClass}' doit étendre la classe Middleware.");
            }

            // Exécution du middleware
            // Si le middleware retourne false, on arrête le traitement
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
