<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class DBConnection
{
    public static function tryConnect(): ?PDO
    {
        $host = getenv('MYSQL_HOST');
        $db = getenv('MYSQL_DB');
        $user = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASS');

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ]);

            if ($pdo) {
                echo "Connection successful\n";
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage() . PHP_EOL;
        }

        return $pdo;
    }
}
