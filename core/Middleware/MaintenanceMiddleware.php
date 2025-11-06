<?php

namespace Core\Middleware;


if (!defined('SECURE_CHECK'))
    die('Direct access not permitted');

use Core\Middleware;

class MaintenanceMiddleware extends Middleware
{
    /**
     * Gère la maintenance du site
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Vérification de l'état de maintenance
        $maintenance = $_ENV['APP_MAINTENANCE'] === 'true';

        // Exemple : autoriser uniquement l’IP de développement
        $allowedIps = ['127.0.0.1', '::1'];

        // Si le site est en maintenance et que l'IP n'est pas autorisée
        // on affiche une page de maintenance
        if ($maintenance && !in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
            http_response_code(503);
            echo file_get_contents(__DIR__ . '/../../App/Views/error/503.html');
            return false; // Bloque la suite du traitement
        }

        return true; // Continue vers le contrôleur
    }
}
