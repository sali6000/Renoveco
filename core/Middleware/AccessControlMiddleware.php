<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\RouteContext;
use App\Core\Logger\AccessLogger;


class AccessControlMiddleware extends Middleware
{
    public function handle(): bool
    {
        // Si l'utilisateur n'a pas de rôle, on le considère comme "guest"
        $role = $_SESSION['user']['role'] ?? 'guest';

        // On utilise RouteContext pour obtenir le contrôleur et l'action actuels
        $route = RouteContext::get(); // ex: ProductController@detail

        // On charge la liste blanche depuis le fichier de configuration
        $whitelist = require __DIR__ . '/../../Config/access_whitelist.php';

        // On récupère la liste des routes autorisées pour le rôle de l'utilisateur
        $allowedRoutes = $whitelist[$role] ?? [];

        // Log de la route demandée pour le rôle de l'utilisateur
        AccessLogger::log("➡️ [$role] Route demandée : $route");

        // Si la route récupérée n'est pas inscrite dans la liste des routes autorisées pour le rôle de l'utilisateur,
        // on redirige vers une page d'erreur 403
        if (!in_array('*', $allowedRoutes) && !in_array($route, $allowedRoutes)) {
            echo "⛔ Accès refusé à la route $route pour le rôle $role";
            AccessLogger::log("⛔ [$role] Accès refusé à la route $route pour le rôle $role");
            exit;
        }

        // Si la route est autorisée, on continue le traitement
        return true;
    }
}
