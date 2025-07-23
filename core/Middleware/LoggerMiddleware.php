<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\Logger\AccessLogger;

class LoggerMiddleware extends Middleware
{
    /**
     * Handle the request and log the access details.
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Vérifie que les variables serveur nécessaires sont disponibles
        if (!isset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['REMOTE_ADDR'])) {
            return false;
        }

        // Préparer un message de log structuré
        $message = sprintf(
            "➡️ %s %s | IP: %s | Agent: %s",
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? 'n/a'
        );

        // Appel du logger centralisé
        AccessLogger::log($message);

        return true;
    }
}
