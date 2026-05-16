<?php

namespace Config;

use Core\Support\DebugHelper;

final class KeysManager
{
    public static function getKeyPath(string $env): ?string
    {
        $path = AppConfig::getConst('ROOT_PATH_STORAGE_SECURE') . "env.$env.key";
        if (file_exists($path)) {
            return $path;
        } else {
            DebugHelper::verboseServer("La clé de decryptage env.$env.key n'a pas été trouvée dans " . AppConfig::getConst('ROOT_PATH_STORAGE_SECURE'));
            return null;
        }
    }
}
