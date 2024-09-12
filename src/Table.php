<?php

namespace PageAnalyzer;

class Table
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert($name, $create)
    {
        $sql = 'INSERT INTO urls(name, created_at) VALUES(:name, :create)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':create', $create);
        $stmt->execute();
        return $this->pdo->lastInsertId('urls_id_seq');
    }

    public function selectAll()
    {
        $sql = 'SELECT id, name, created_at FROM urls ORDER BY created_at DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
