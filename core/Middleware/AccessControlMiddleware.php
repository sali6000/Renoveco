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
        // Récupération du contrôleur + action actuel (ex: Services\ServiceDetailController@detail)

        $route = RouteContext::getInstance()->getClass() . '@' . RouteContext::getInstance()->getAction();


        // Récupération du rôle actuel de l'utilisateur
        $role = $_SESSION['user']['role'] ?? 'guest';

        // Récupération de la liste d'accès
        $config      = AppConfig::getWhitelist();
        $permissions = $config['permissions'] ?? [];
        $hierarchy   = $config['roles'] ?? [];

        // Vérification de l'accès de l'utilsateur à la route demandée
        $allowedRoutes = $this->resolvePermissions($_SESSION['user']['role'] ?? 'guest', $permissions, $hierarchy);

        if (!in_array('*', $allowedRoutes, true) && !in_array($route, $allowedRoutes, true)) {
            AccessLogger::logTo("Accès refusé pour la route $route en tant que [$role]", AccessLogger::LEVEL_WARNING, AccessLogger::CHANNEL_ROUTING);
            http_response_code(403);
            echo "Accès refusé en tant que $role";
            exit;
        }
        return true;
    }

    /**
     * Résout récursivement toutes les permissions d'un rôle
     * en remontant la chaîne d'héritage.
     *
     * @param array<string, array<string>> $permissions
     * @param array<string, array<string>> $hierarchy
     * @return array<string>
     */
    private function resolvePermissions(
        string $role,
        array $permissions,
        array $hierarchy,
        array &$visited = []
    ): array {
        // Protection anti-boucle infinie : si ce rôle a déjà été visité durant
        // cette résolution (ex: A hérite de B qui hérite de A), on sort immédiatement
        if (in_array($role, $visited, true)) {
            return [];
        }

        // On marque ce rôle comme visité pour les appels récursifs suivants
        $visited[] = $role;

        // On récupère les permissions déclarées directement pour ce rôle
        // ex: 'user' => ['User\ProfileController@view', 'Order\OrderController@list']
        $ownPermissions = $permissions[$role] ?? [];

        // Si ce rôle a un accès total (*), inutile de remonter les parents
        // on retourne immédiatement ['*'] qui court-circuite tout
        if (in_array('*', $ownPermissions, true)) {
            return ['*'];
        }

        // On va collecter les permissions héritées des rôles parents
        // ex: 'user' hérite de 'guest' → on résout récursivement 'guest'
        $inherited = [];
        foreach ($hierarchy[$role] ?? [] as $parentRole) {
            $inherited = array_merge(
                $inherited,
                // Appel récursif : on résout le parent, qui lui-même résoudra ses propres parents
                // ex: user → guest → (pas de parent) → retourne les permissions guest
                $this->resolvePermissions($parentRole, $permissions, $hierarchy, $visited)
            );
        }

        // On fusionne les permissions propres + héritées, sans doublons
        // ex: ['User\ProfileController@view'] + ['Home\HomeIndexController@index', ...]
        return array_unique(array_merge($ownPermissions, $inherited));
    }
}
