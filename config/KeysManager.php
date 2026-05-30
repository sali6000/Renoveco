<?php

namespace Config;

final class KeysManager
{
    public static function getKeyPath(string $env): ?string
    {
        $path = AppConfig::getConst('ROOT_PATH_STORAGE_SECURE') . "env.$env.key";
        if (file_exists($path)) {
            return $path;
        } else {
            return null;
        }
    }
}
