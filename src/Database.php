<?php declare(strict_types=1);

namespace Zycus;

use PDO;

final class Database
{
    private static ?PDO $instance = null;

    public static function pdo(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $host = Config::require('DB_HOST');
        $name = Config::require('DB_NAME');
        $user = Config::require('DB_USER');
        $pass = (string) Config::get('DB_PASS', '');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

        $timeout = (int) Config::get('DB_TIMEOUT', 5);

        self::$instance = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => $timeout > 0 ? $timeout : 5,
        ]);

        return self::$instance;
    }
}
