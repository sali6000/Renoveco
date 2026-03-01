<?php
// core/Router.php

namespace Core\Routing;

use App\Exception\ValidationException;
use Config\AppConfig;
use Core\Container;
use Core\Middleware\Middleware;
use Core\Routing\RouteCache;
use Core\Routing\RouteCompiler;
use Core\Routing\RouteContext;
use Exception;

class Router
{
    private string $_uri;
    private Container $container;

    public function __construct($uri, $container) // /product/list
    {
        $this->uri = $uri;
        $this->container = $container;
    }

    public string $uri {
        get => $this->_uri;
        set(string $value) => $this->_uri = $value;
    }

    public function route()
    {
        // Parcourir le cache
        foreach ($this->loadRoutesCache() as $cache) {

            // Si l'URI correspond à un pattern en cache
            if ($pattern = $this->validationURI($cache['pattern'], $this->uri)) {

                // Récupérer les paramètres inscrits dans l'URI selon le cache
                $params = $this->convertUriToParams($cache['params'], $pattern);

                // Sauvegarder le context complet de la route à partir du cache
                // En y intégrant les paramètres et la méthode
                $this->saveRouteContext($cache, $params, $_SERVER['REQUEST_METHOD']);

                // Vérifier l'autorisation (guest? maintenance ? ...) 
                $this->validationRouteContext(
                    RouteContext::getInstance()->getController(),
                    RouteContext::getInstance()->getAction()
                );

                // Lancer la route
                $this->launchRouteContext(
                    RouteContext::getInstance()->getClass(),
                    RouteContext::getInstance()->getAction(),
                    RouteContext::getInstance()->getParams()
                );

                // Fin
                return;
            }
        }

        // Renvoyer une erreur si URI inconnu
        throw new Exception('Aucun pattern en cache de trouvé pour l\'URI : ' . $this->uri);
    }

    private function saveRouteContext($cache, $params, $method)
    {
        // Sauvegarder le context complet de la route (avec les parametres)
        RouteContext::getInstance()->setRouteContext(
            $cache['controller'],
            $cache['action'],
            $cache['class'],
            $params,
            $method
        );
    }

    private function loadRoutesCache()
    {
        // Récupérer le cache
        $cache = RouteCache::exists() ? RouteCache::load() : RouteCompiler::compile();

        // Retourner uniquement les routes ayant la méthode demandée
        return $cache['routes'][$_SERVER['REQUEST_METHOD']];
    }

    private function validationURI(string $cachePattern, string $uri): ?array
    {
        // Vérifier via regex si l'URI correspond à un pattern en cache. Exemple:
        // /product/detail/pe50-105426586987 == #^/product/detail/(?P<slug>[^/]+)$# 
        if (preg_match($cachePattern, $uri, $matches)) {

            /**
             * Si match, retourner les valeurs associées. Exemple:
             * [0] => /product/detail/chassis-de-fenetres-pe50-105426586987
             * [slug] => chassis-de-fenetres-pe50-105426586987
             * [1] => chassis-de-fenetres-pe50-105426586987
             */
            return $matches;
        }
        return null;
    }

    private function convertUriToParams(array $cacheParams, array $uriParams): array
    {
        $params = [];

        // Pour chaque paramètre défini dans le cache de la route
        foreach ($cacheParams as $cacheParam) {

            // Récupérer les parametres correspondants provenants de l'URI
            $params[] = $uriParams[$cacheParam] ?? null;
        }

        // [0] => chassis-de-fenetres-pe50-105426586987
        return $params;
    }

    private function validationRouteContext($controller, $action)
    {
        // Préparer la route à vérifier
        $keysToCheck = [
            $controller . '@' . $action,
            $controller . '@*',
            '*@*'
        ];

        // Vérifier la route
        foreach ($keysToCheck as $key) {
            if (!$this->validationMiddlewares($key)) {
                throw new ValidationException('Middleware bloquant la requête');
            }
        }
    }

    private function validationMiddlewares(string $key): bool
    {
        // Chargement des middlewares personnalisées
        $middlewares  = require AppConfig::getConst('LOCAL_PATH_CONFIG') . 'middlewares.php';

        // Si aucune middleware n'est associée à la clé, on considère que c'est un succès
        if (!isset($middlewares[$key])) return true;

        // Vérification de l'existence des middlewares et de leur type
        foreach ($middlewares[$key] as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new \Exception("Le middleware '{$middlewareClass}' n'existe pas.");
            }

            // Instanciation du middleware
            $middleware = new $middlewareClass();

            // Vérification que l'instance est bien un Middleware
            if (!$middleware instanceof Middleware) {
                throw new \Exception("Le middleware '{$middlewareClass}' doit étendre la classe Middleware.");
            }

            // Si le middleware retourne false, on arrête le traitement
            if (!$middleware->handle()) {
                return false;
            }
        }
        return true;
    }

    private function launchRouteContext($class, $action, $params)
    {
        $controller = $this->container->get($class);
        call_user_func_array(
            [$controller, $action],
            $params
        );
    }
}
