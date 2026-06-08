<?php
// Core/Support/DebugHelper

namespace Core\Support;

use Config\AppConfig;
use Core\Logger\AccessLogger;

final class DebugHelper
{
    /**
     * Log technique très verbeux dans le fichier DEBUG (<date>-debug.log).
     *
     * @param mixed $data Donnée à logger
     * @param bool $show Si false, ignore silencieusement le log
     */
    public static function verboseServer(mixed $data, bool $show = true): void
    {
        if (!$show) {
            return;
        }

        // Préfixe utile pour repérer les logs de debug "dev"
        $prefix = "[DEBUGHELPER] => ";

        // Convertir en string propre
        if (is_array($data) || is_object($data)) {
            $str = print_r($data, true);
        } elseif (is_bool($data)) {
            $str = $data ? 'true' : 'false';
        } elseif ($data === null) {
            $str = 'null';
        } else {
            $str = (string)$data;
        }

        AccessLogger::logTo($prefix . $str, AccessLogger::LEVEL_DEBUG, AccessLogger::CHANNEL_DEBUG, false);
    }

    /**
     * Vérifie la validité d'une variable selon plusieurs critères.
     * Ex: isTrue('DB_PATH', $chemin, ['type' => 'string', 'must_exist' => true, 'not_empty' => true, 'required' => true]);
     *
     * @param string $name Nom de la variable (pour le message d'erreur)
     * @param mixed $value Valeur à vérifier
     * @param array $options Options de validation :
     *  - required: bool
     *  - type: string (ex: 'string', 'array', 'file')
     *  - not_empty: bool
     *  - must_exist: bool (si type = string et représente un fichier chemin)
     */
    public static function isTrue(string $name, $value, array $options = []): void
    {
        $required = $options['required'] ?? true; // Tester si la variable est null ou non
        $expectedType = $options['type'] ?? null; // Type attendu (string, array, etc.)
        $notEmpty = $options['not_empty'] ?? false; // Tester si la variable n'est pas vide
        $mustExist = $options['must_exist'] ?? false; // Tester si le fichier/chemin existe

        if ($required && is_null($value)) {
            throw new \RuntimeException("❌ La variable [$name] est requise mais vaut null.");
        }

        if ($expectedType && gettype($value) !== $expectedType) {
            throw new \RuntimeException("❌ La variable [$name] doit être de type [$expectedType], mais vaut [" . gettype($value) . "].");
        }

        if ($notEmpty) {
            if (is_string($value) && trim($value) === '') {
                throw new \RuntimeException("❌ La variable [$name] est une chaîne vide.");
            } elseif (is_array($value) && count($value) === 0) {
                throw new \RuntimeException("❌ La variable [$name] est un tableau vide.");
            }
        }

        if ($mustExist && is_string($value)) {
            if (!file_exists($value)) {
                throw new \RuntimeException("❌ Le chemin [$value] (variable [$name]) n'existe pas.");
            }
            if (is_file($value) && filesize($value) === 0) {
                throw new \RuntimeException("⚠️ Le fichier [$value] (variable [$name]) est vide.");
            }
        }
    }

    /**
     * Log une requête SQL avec placeholders et paramètres, en console ou dans un fichier.
     *
     * @param string $query   La requête SQL avec placeholders (:slug)
     * @param array  $params  Les paramètres de la requête [':slug' => 'value']
     * @param bool   $toFile  Si true, log dans un fichier (storage/logs/sql.log)
     */
    public static function logSQL(string $query, array $params = [], bool $toFile = false): void
    {
        $interpolated = self::interpolateQuery($query, $params);

        if ($toFile) {
            self::logToFile($query, $params, $interpolated);
        } else {
            self::logToConsole($query, $params, $interpolated);
        }
    }

    /**
     * Interpole les paramètres dans la requête (pour debug uniquement)
     */
    private static function interpolateQuery(string $query, array $params): string
    {
        foreach ($params as $key => $value) {
            // s'assurer que le placeholder contient le ":" (ex: ':slug')
            $placeholder = (strpos($key, ':') === 0) ? $key : ':' . $key;

            // formater la valeur selon son type (NULL, bool, number, string, array, sensitive)
            $safeValue = self::formatSQLValue($key, $value);

            // remplacer strictement le placeholder (avec les deux-points) seulement
            $query = str_replace($placeholder, $safeValue, $query);
        }

        return $query;
    }


    /**
     * Formate les valeurs en fonction de leur type
     */
    private static function formatSQLValue(string $key, $value): string
    {
        // Masquage automatique de données sensibles
        $sensitive = ['password', 'pass', 'token', 'apikey', 'secret'];
        foreach ($sensitive as $s) {
            if (stripos($key, $s) !== false) {
                return "'********'";
            }
        }

        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value)) {
            // ex: [1,2] -> (1,2)  ou ['a','b'] -> ('a','b')
            $items = array_map(function ($v) {
                if ($v === null) return 'NULL';
                if (is_bool($v)) return $v ? '1' : '0';
                if (is_numeric($v)) return (string)$v;
                return "'" . addslashes((string)$v) . "'";
            }, $value);
            return '(' . implode(', ', $items) . ')';
        }
        if (is_numeric($value)) {
            return (string)$value;
        }

        // Échappement pour éviter de casser le SQL
        return "'" . addslashes((string)$value) . "'";
    }


    /**
     * Affiche en console JS (dev)
     */
    private static function logToConsole(string $query, array $params, string $interpolated): void
    {
        $queryPretty = preg_replace('/\s+/', ' ', trim($query));
        $interpolatedPretty = preg_replace('/\s+/', ' ', trim($interpolated));

        echo "<script>console.log('%c[SQL RAW]', 'color:#888;', " . json_encode($queryPretty) . ");</script>";
        echo "<script>console.log('%c[SQL PARAMS]', 'color:#06f;', " . json_encode($params) . ");</script>";
        echo "<script>console.log('%c[SQL FULL]', 'color:#0a0;font-weight:bold;', " . json_encode($interpolatedPretty) . ");</script>";
    }


    /**
     * Log dans un fichier (préprod/prod)
     */
    private static function logToFile(string $query, array $params, string $interpolated): void
    {
        $logDir = AppConfig::getConst('ROOT_PATH_STORAGE_LOGS');
        $logFile = $logDir . 'sql.log';

        $log = "---------------------\n";
        $log .= "[" . date('Y-m-d H:i:s') . "] SQL LOG\n";
        $log .= "RAW: $query\n";
        $log .= "PARAMS: " . json_encode($params) . "\n";
        $log .= "FULL: $interpolated\n";

        file_put_contents($logFile, $log, FILE_APPEND);
    }
}
