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

    public function getId($url): ?int
    {
        $sql = 'SELECT id FROM urls WHERE name = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$url]);
        $id = $stmt->fetch();
        if ($id) {
            return $id['id'];
        } else {
            return null;
        }
    }

    public function selectAll(): ?array
    {
        $sql = 'SELECT DISTINCT on (urls.id)
                    urls.id,
                    urls.name,
                    url_checks.created_at,
                    url_checks.status_code
                FROM urls
                LEFT JOIN url_checks ON
                    urls.id = url_checks.url_id';
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

    public function insertCheck($id, $status, $h, $title, $description, $create): int
    {
        $sql = 'INSERT INTO
                    url_checks(url_id, status_code, h1, title, description, created_at)
                    VALUES(:url_id, :status_code, :h1, :title, :description, :create)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url_id', $id);
        $stmt->bindValue(':status_code', $status);
        $stmt->bindValue(':h1', $h);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':create', $create);
        $stmt->execute();
        return $this->pdo->lastInsertId('url_checks_id_seq');
    }

    public function selectAllCheck($id): ?array
    {
        $sql = 'SELECT 
                    id,
                    url_id,
                    status_code,
                    h1,
                    title,
                    description,
                    created_at
                FROM url_checks
                WHERE url_id = ?
                ORDER BY id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
