<?php
// core/Middleware/AdminMiddleware.php

namespace Core\Middleware;

use Core\Middleware;

class AdminMiddleware extends Middleware
{
    public function handle(): bool
    {
        // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'superadmin'])) {
            http_response_code(403);
            echo "Accès refusé. Vous devez être administrateur pour accéder à cette ressource. Actuellement, vous êtes: ";
            if (isset($_SESSION['user']))
                echo $_SESSION['user']['role'];
            return false;
        }
        return true;
    }
}
