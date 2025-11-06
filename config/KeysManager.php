<?php

namespace Config;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

final class KeysManager
{
    public static function getKeyPath(string $env): ?string
    {
        $path = "/var/www/secure/env.$env.key";
        return file_exists($path) ? $path : null;
    }
}
