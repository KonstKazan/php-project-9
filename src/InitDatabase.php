<?php

namespace PageAnalyzer;

class InitDatabase
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function initTables()
    {
        $initFilePath = implode('/', [dirname(__DIR__), 'init.sql']);
        $initSql = file_get_contents($initFilePath);
        $this->pdo->exec($initSql);
    }
}
