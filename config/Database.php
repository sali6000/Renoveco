<?php

namespace Config;

if (!defined('SECURE_CHECK')) {
  die('Direct access not permitted');
}

use PDO;
use PDOException;

class Database
{
  private static $instance = null;

  private function __construct() {}

  public static function getInstance()
  {
    if (self::$instance === null) {
      try {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4'; // PHP sait qu’il doit envoyer les données en UTF-8
        self::$instance = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
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
