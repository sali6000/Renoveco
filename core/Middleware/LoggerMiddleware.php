<?php

namespace Core\Middleware;

use Core\Middleware\Middleware;
use Core\Logger\AccessLogger;

class LoggerMiddleware extends Middleware
{
    /**
     * Handle the request and log the access details.
     *
     * @return bool
     */
    public function handle(): bool
    {
        // Appel du logger centralisé
        //AccessLogger::log("---> Début de la demande de route le " . date('Y-m-d H:i:s') . " :", AccessLogger::LEVEL_INFO);
        return true;
    }
}
