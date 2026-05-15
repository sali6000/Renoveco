<?php

namespace Core\Middleware;

use Core\Middleware\Middleware;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

class AuthMiddleware extends Middleware
{
    public function __construct(private readonly SessionManager $session) {}

    public function handle(): bool
    {
        if (!$this->session->isAuthenticated()) {
            $this->session->setIntendedRedirect($_SERVER['REQUEST_URI']);
            header('Location: /auth/login');
            exit;
        }
        return true;
    }
}
