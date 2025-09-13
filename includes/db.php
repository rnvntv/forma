<?php
class DB {
  private static ?PDO $pdo = null;
  public static function conn(): PDO {
    if (self::$pdo) return self::$pdo;
    $cfg = require __DIR__ . '/config.php';
    $dsn = 'mysql:host=' . $cfg['db_host'] . ';dbname=' . $cfg['db_name'] . ';charset=utf8mb4';
    $opt = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    self::$pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], $opt);
    self::$pdo->exec("SET time_zone = '+00:00'");
    return self::$pdo;
  }
}
