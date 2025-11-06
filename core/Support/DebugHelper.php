<?php

namespace Core\Support;

use Config\AppConfig;

final class DebugHelper
{

    /**
     * DebugHelper::verbose('NomDeLaVariable: ', $maVariable); // exemple d'appel à cette méthode
     * Affiche une analyse complète d'une variable au format HTML.
     * @param string $name Nom de la variable pour affichage.
     * @param mixed $value La valeur à analyser.
     */
    public static function verboseHtml(string $name, array $values, bool $show = true, int $depth = 0): void
    {
        $debug = filter_var($_ENV['DEBUG_VERBOSE'] ?? false, FILTER_VALIDATE_BOOL);

        if (!$debug || !$show) {
            return;
        }

        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $depth);

        // En-tête principale
        if ($depth === 0) {
            echo "------ DEBUG CONTEXTE => <span style='color:red;font-weight:bold;'>" . strtoupper($name) . "</span> ------<br>";
        }

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                echo "<span style='color:orange;font-weight:bold;'>" . $key . "</span> => " . gettype($value) . " => ";
                // --- Types de base ---
                if (is_string($value)) {;
                    echo "<span style='color:green;font-weight:bold;'>" . $value . "</span><br>";
                } elseif (is_array($value)) {
                    echo $indent . "(<span style='color:green;font-weight:bold;'>" . count($value) . "</span> éléments):<br>";
                    if (count($value) > 0) echo "[------------------------ Array (debut) ------------------------]<br>";
                    foreach ($value as $key2 => $val) {
                        echo $key . "<span style='color:orange;font-weight:bold;'>[" . $key2 . "]</span> => ";
                        if (is_array($val) || is_object($val)) {
                            echo "<br>";
                            self::verboseHtml($name . "[$key2]", $val, $depth + 1);
                        } else {
                            echo  "<span style='color:green;font-weight:bold;'>" . htmlspecialchars(print_r($val, true)) . "</span><br>";
                        }
                    }
                    if (count($value) > 0) echo "[------------------------ Array (fin) ----------------------------]<br>";
                } elseif (is_bool($value)) {
                    $color = $value ? 'green' : 'red';
                    echo $indent . "<span style='color:$color;font-weight:bold;'>" . ($value ? 'true' : 'false') . "</span><br>";
                } elseif (is_object($value)) {
                    $className = get_class($value);
                    echo $indent . "=> Classe : <b>$className</b><br>";
                } elseif ($value === null) {
                    echo $indent . "<span style='color:red;font-weight:bold;'>NULL</span><br>";
                } else {
                    echo $indent . "<span style='color:red;font-weight:bold;'>";
                    echo htmlspecialchars(print_r($value, true)) . "</span><br>";
                }
            }
            echo "<br>";
        } else {
            echo "<span style='color:orange;font-weight:bold;'>" . $name . "</span> => " . gettype($values) . " => <span style='color:green;font-weight:bold;'>" . $values . "</span><br><br>";
        }
    }


    /**
     * Affiche une analyse complète d'une variable au format JS dans la console du navigateur. (via F12)
     * @param string $name Nom de la variable pour affichage.
     * @param mixed $value La valeur à analyser.
     */
    public static function verboseJS(string $name, $value): void
    {
        if (!($_ENV['DEBUG_VERBOSE'] ?? false)) return;

        $js = "<script>console.groupCollapsed('🧠 DEBUG: " . addslashes($name) . "');";

        $type = gettype($value);
        $js .= "console.log('📦 Type:', '$type');";

        if (is_string($value)) {
            $length = strlen($value);
            $isEmpty = trim($value) === '';
            $js .= "console.log('🧵 Longueur:', $length);";
            $js .= "console.log('❔ Est vide ?:', '" . ($isEmpty ? 'Oui ✅' : 'Non ❌') . "');";
            $js .= "console.log('📜 Contenu:', " . json_encode($value) . ");";

            if (file_exists($value)) {
                $js .= "console.log('📁 Chemin fichier : Oui ✅');";
                $js .= "console.log('📄 Fichier lisible : " . (is_readable($value) ? 'Oui ✅' : 'Non ❌') . "');";
                $js .= "console.log('🈳 Fichier vide ? : " . (filesize($value) === 0 ? 'Oui ✅' : 'Non ❌') . "');";
            } else {
                $js .= "console.log('📁 Chemin fichier : Non ❌');";
            }
        } elseif (is_array($value)) {
            $js .= "console.log('📦 Taille:', " . count($value) . ");";
            $js .= "console.log('📜 Contenu:', " . json_encode($value, JSON_PRETTY_PRINT) . ");";
        } elseif (is_bool($value)) {
            $js .= "console.log('🔘 Booléen:', " . ($value ? 'true' : 'false') . ");";
        } elseif (is_object($value)) {
            $className = get_class($value);
            $js .= "console.log('🏷️ Classe:', '$className');";

            $props = get_object_vars($value);
            $methods = get_class_methods($value);
            $js .= "console.log('🔧 Propriétés:', " . json_encode($props, JSON_PRETTY_PRINT) . ");";
            $js .= "console.log('🧰 Méthodes:', " . json_encode($methods, JSON_PRETTY_PRINT) . ");";
        } else {
            ob_start();
            var_dump($value);
            $dump = trim(ob_get_clean());
            $js .= "console.log('📜 Dump brut:', " . json_encode($dump) . ");";
        }

        $js .= "console.groupEnd();</script>";
        echo $js;
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
        $logDir = AppConfig::getPath('APP_PATH_LOCAL_STORAGE_LOGS');
        $logFile = $logDir . 'sql.log';

        $log = "---------------------\n";
        $log .= "[" . date('Y-m-d H:i:s') . "] SQL LOG\n";
        $log .= "RAW: $query\n";
        $log .= "PARAMS: " . json_encode($params) . "\n";
        $log .= "FULL: $interpolated\n";

        file_put_contents($logFile, $log, FILE_APPEND);
    }
}
