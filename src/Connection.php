<?php

namespace PageAnalyzer;

class Connection
{
    public function connect()
    {
        if (isset($_ENV['DATABASE_URL'])) {
            $databaseUrl = parse_url($_ENV['DATABASE_URL']);
            $username = $databaseUrl['user'];
            $password = $databaseUrl['pass'];
            $host = $databaseUrl['host'];
            $port = $databaseUrl['port'];
            $dbName = ltrim($databaseUrl['path'], '/');
        } else {
            $databaseUrl = parse_url('postgresql://mydb_92qj_user:TAjKdmOqarvpndq0BrH83GW3UaX3uJoy@dpg-crimqgrv2p9s738kpvg0-a.oregon-postgres.render.com:5432/mydb_92qj');
            $username = $databaseUrl['user'];
            $password = $databaseUrl['pass'];
            $host = $databaseUrl['host'];
            $port = $databaseUrl['port'];
            $dbName = ltrim($databaseUrl['path'], '/');
        }

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
