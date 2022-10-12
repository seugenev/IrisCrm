<?php

namespace Importer\app\core\DB;

use PDO;

class Connection
{
    private static ?PDO $connection = null;

    public static function getConnection(string $host, string $name, string $user, string $password): PDO
    {
        if (static::$connection === null) {
            static::$connection = (new DatabaseConnectionFactory())->makeConnection($host, $name, $user, $password);
        }
        return static::$connection;
    }

    private function __construct(){}
    private function __clone(){}
}
