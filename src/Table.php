<?php

namespace PageAnalyzer;

class Table
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert($name, $create): int
    {
        $sql = 'INSERT INTO urls(name, created_at) VALUES(:name, :create)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':create', $create);
        $stmt->execute();
        return $this->pdo->lastInsertId('urls_id_seq');
    }

    public function getId($url)
    {
        $sql = 'SELECT id FROM urls WHERE name = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$url]);
        $id = $stmt->fetch();
        return $id['id'];
    }

    public function selectAll(): ?array
    {
        $sql = 'SELECT id, name, created_at FROM urls ORDER BY created_at DESC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function select($id): ?array
    {
        $sql = 'SELECT * FROM urls WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
