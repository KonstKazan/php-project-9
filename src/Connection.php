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
}
