<?php

namespace App;

use PDO;

class Db
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $host = Env::get('DB_HOST', 'localhost');
            $name = Env::get('DB_NAME');
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            self::$pdo = new PDO($dsn, Env::get('DB_USER'), Env::get('DB_PASS'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
