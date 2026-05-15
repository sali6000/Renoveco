<?php

declare(strict_types=1);

namespace Src\Modules\Shared\Infrastructure\Http\Session;

final class SessionManager
{
    private const USER_KEY          = 'user';
    private const REDIRECT_KEY      = 'redirect_after_login';
    private const DEFAULT_REDIRECT  = '/home';

    public function openAuthenticatedSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::USER_KEY] = $user;
    }

    public function closeAuthenticatedSession(): void
    {
        unset($_SESSION[self::USER_KEY]);
        session_regenerate_id(true);
    }

    public function getAuthenticatedUser(): ?array
    {
        return $_SESSION[self::USER_KEY] ?? null;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION[self::USER_KEY]);
    }

    public function consumeRedirect(): string
    {
        $redirect = $_SESSION[self::REDIRECT_KEY] ?? self::DEFAULT_REDIRECT;
        unset($_SESSION[self::REDIRECT_KEY]);

        return $redirect;
    }

    public function setIntendedRedirect(string $url): void
    {
        $_SESSION[self::REDIRECT_KEY] = $url;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
