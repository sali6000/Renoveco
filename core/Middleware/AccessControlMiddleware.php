<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\RouteContext;
use Core\Logger\AccessLogger;
use Config\AppConfig;

class AccessControlMiddleware extends Middleware
{
    public function handle(): bool
    {
        // 🔐 1. Détection du rôle utilisateur (default: guest)
        $role = $_SESSION['user']['role'] ?? 'guest';

        // 📍 2. Récupération du contrôleur + action actuel (ex: ProductController@detail)
        $route = RouteContext::get();

        // ✅ 3. Chargement de la whitelist via AppConfig
        $whitelist = AppConfig::getWhitelist();

        // 🧾 4. Récupération des routes autorisées pour ce rôle
        $allowedRoutes = $whitelist[$role] ?? [];

        // 📒 5. Log d’accès (facultatif mais très pro)
        AccessLogger::log("Tentative d'accès en tant que [$role] pour la route suivante : $route", AccessLogger::LEVEL_INFO);

        // ⛔ 6. Vérification d’accès
        if (!in_array('*', $allowedRoutes) && !in_array($route, $allowedRoutes)) {
            echo "Accès refusé en tant que $role";
            AccessLogger::log("Accès refusé pour la route $route en tant que [$role]", AccessLogger::LEVEL_WARNING);
            http_response_code(403);
            exit;
        }
        AccessLogger::log("Accès autorisé pour la route $route en tant que [$role]", AccessLogger::LEVEL_INFO);
        // ✅ 7. Accès autorisé : continuer la chaîne de middleware
        return true;
    }
}
