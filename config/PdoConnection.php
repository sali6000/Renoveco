<?php

namespace Config;

use PDO;

class PdoConnection
{
  private static ?PDO $instance = null;

  private function __construct() {}

  // Permet de récupérer la base de données
  // Si la connexion n'est pas encore établie, elle est créée via la classe Database
  public static function connection(): PDO
  {
    if (self::$instance === null) {
      $host = AppConfig::getEnv('DB_HOST');
      $db = AppConfig::getEnv('DB_NAME');
      $username = AppConfig::getEnv('DB_USERNAME');
      $password = AppConfig::getEnv('DB_PASSWORD');
      $charset = AppConfig::getEnv('DB_CHARSET');
      $dsn = 'mysql:host=' . $host . ';dbname=' . $db . ';charset=' . $charset;

      self::$instance = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
      ]);
    }
    return self::$instance;
  }
}
