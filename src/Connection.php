<?php

namespace PageAnalyzer;

class Connection
{
    public function connect()
    {

        $conn = new \PDO('pgsql:host=localhost;dbname=mydb;user=konstantin;password=konstantin');
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
