<?php

namespace PageAnalyzer;

class Table
{
    private $pdo;
    private $name;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert($name)
    {
        $sql = 'INSERT INTO urls(name) VALUES(:name)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return $this->pdo->lastInsertId('labels_id_seq');
    }
}
