<?php

namespace Core\Middleware;

use Core\Middleware\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /auth/login');
            exit;
        }

        return true;
    }
}
