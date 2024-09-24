<?php

namespace PageAnalyzer;

use Dotenv\Dotenv;
use PDO;

class Connection
{
    public function connect(): PDO
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
        if (isset($_ENV['DATABASE_URL'])) {
            $databaseUrl = parse_url($_ENV['DATABASE_URL']);
        } else {
            throw new \Exception("Database connection error!");
        }
        $username = $databaseUrl['user'];
        $password = $databaseUrl['pass'];
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'];
        $dbName = ltrim($databaseUrl['path'], '/');

        $conn = new \PDO("pgsql:host=$host;port=$port;dbname=$dbName;user=$username;password=$password");
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    public function initTables(PDO $pdo): void
    {
        $initFilePath = implode('/', [dirname(__DIR__), 'database.sql']);
        $initSql = file_get_contents($initFilePath);
        if ($initSql === false) {
            throw new \Exception("Path to file is invalid!");
        }
        $pdo->exec($initSql);
    }
}
