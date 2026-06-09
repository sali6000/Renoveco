<?php

namespace Src\Modules\Shared\Infrastructure\Schema;

class HelperSchemaMysql
{

    /**
     * Retourne la propriété sans préfixe ex: "rol.name" devient "name"
     */
    public static function fieldProperty(string $fieldScheme): string
    {
        $parts = explode('.', $fieldScheme);
        return end($parts); // retourne le dernier segment
    }

    /**
     * Retourne la table sans préfixe ex: "role rol" devient "role"
     */
    public static function fieldTable(string $tableScheme): string
    {
        return preg_split('/\s+/', trim($tableScheme))[0];
    }
}
