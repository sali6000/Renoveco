<?php

namespace Config;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use Core\Support\DebugHelper;
use PDO;
use PDOException;

class PdoConnection
{
  private static ?PDO $instance = null;

  private function __construct() {}

  // Permet de récupérer la base de données
  // Si la connexion n'est pas encore établie, elle est créée via la classe Database
  public static function connection(): PDO
  {
    if (self::$instance === null) {
      try {
        $host = AppConfig::getEnv('DB_HOST');
        $db = AppConfig::getEnv('DB_NAME');
        $username = AppConfig::getEnv('DB_USERNAME');
        $password = AppConfig::getEnv('DB_PASSWORD');
        $charset = AppConfig::getEnv('DB_CHARSET');
        $dsn = 'mysql:host=' . $host . ';dbname=' . $db . ';charset=' . $charset;

        self::$instance = new PDO($dsn, $username, $password, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // Garantit que MySQL reçoit bien les octets en UTF-8
        ]);
      } catch (PDOException $e) {
        throw new \Exception('Database connection error: ' . $e->getMessage());
      }
    }
    return self::$instance;
  }
}
