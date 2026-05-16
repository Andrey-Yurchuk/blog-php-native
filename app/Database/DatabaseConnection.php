<?php

declare(strict_types=1);

namespace App\Database;

use App\Support\Env;
use PDO;

final class DatabaseConnection
{
    public static function create(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            Env::getString('DB_HOST'),
            Env::getInt('DB_INTERNAL_PORT'),
            Env::getString('DB_DATABASE'),
        );

        $pdo = new PDO(
            $dsn,
            Env::getString('DB_USERNAME'),
            Env::getString('DB_PASSWORD'),
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }
}
