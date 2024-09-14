<?php

namespace PageAnalyzer;

use Dotenv\Dotenv;

class Connection
{
    public function connect()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
        if (isset($_ENV['DATABASE_URL'])) {
            $databaseUrl = parse_url($_ENV['DATABASE_URL']);
        } else {
            $databaseUrl = parse_url($_ENV['SECRET_KEY']);
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

    public function initTables($pdo)
    {
        $initFilePath = implode('/', [dirname(__DIR__), 'database.sql']);
        $initSql = file_get_contents($initFilePath);
        $pdo->exec($initSql);
    }
}
