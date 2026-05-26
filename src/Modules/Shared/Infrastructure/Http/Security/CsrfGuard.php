<?php

declare(strict_types=1);

namespace Src\Modules\Shared\Infrastructure\Http\Security;

use Src\Modules\Shared\Infrastructure\Http\Security\CsrfException;

final class CsrfGuard
{
    private const TOKEN_KEY = 'csrf_token';

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_KEY] = $token;

        return $token;
    }

    /**
     * @throws CsrfException Si token invalide
     */
    public function validateOrFail(): void
    {
        if (!$this->isValid()) {
            throw new CsrfException('Token CSRF invalide.');
        }
    }

    public function isValid(): bool
    {
        $submitted = $_POST[self::TOKEN_KEY] ?? null;
        $stored    = $_SESSION[self::TOKEN_KEY] ?? null;

        if ($submitted === null || $stored === null) {
            return false;
        }

        // hash_equals : comparaison en temps constant → protection timing attack
        return hash_equals($stored, $submitted);
    }
}
