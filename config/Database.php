<?php

namespace App\Config;

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
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        self::$instance = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        throw new \Exception('Database connection error: ' . $e->getMessage());
      }
    }
    return self::$instance;
  }
}
