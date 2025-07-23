<?php

namespace App\Core\Middleware;

use App\Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return true;
    }
}
