<?php

namespace Core\Middleware;

use Core\Middleware\Middleware;
use Core\Routing\RouteContext;
use Core\Logger\AccessLogger;
use Config\AppConfig;

class AccessControlMiddleware extends Middleware
{
    public function handle(): bool
    {
        // 🔐 1. Détection du rôle utilisateur (default: guest)
        $role = $_SESSION['user']['role'] ?? 'guest';

        // 📍 2. Récupération du contrôleur + action actuel (ex: ProductController@detail)
        $route = RouteContext::getInstance()->getController() . '@' . RouteContext::getInstance()->getAction();

        // ✅ 3. Chargement de la whitelist via AppConfig
        $whitelist = AppConfig::getWhitelist();

        // 🧾 4. Récupération des routes autorisées pour ce rôle
        $allowedRoutes = $whitelist[$role] ?? [];

        // ⛔ 6. Vérification d’accès
        if (!in_array('*', $allowedRoutes) && !in_array($route, $allowedRoutes)) {
            AccessLogger::log("10. Accès refusé pour la route $route en tant que [$role]", AccessLogger::LEVEL_WARNING);
            http_response_code(403);
            echo "Accès refusé en tant que $role";
            exit;
        }

        // ✅ 7. Accès autorisé : continuer la chaîne de middleware
        return true;
    }
}
