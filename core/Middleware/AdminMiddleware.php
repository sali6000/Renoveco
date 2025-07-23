<?php
// core/Middleware/AdminMiddleware.php

namespace App\Core\Middleware;

use App\Core\Middleware;

class AdminMiddleware extends Middleware
{
    public function handle(): bool
    {
        // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo "Accès refusé. Vous devez être administrateur pour accéder à cette ressource.";
            return false;
        }
        return true;
    }
}
