<?php

namespace PageAnalyzer;

use PDO;

class Urls
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, string $created): string|false
    {
        $sql = 'INSERT INTO urls(name, created_at) VALUES(:name, :create)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':create', $created);
        $stmt->execute();
        return $this->pdo->lastInsertId('urls_id_seq');
    }

    public function getId(string $url): ?int
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

    public function getAll(): ?array
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

    public function get(int $id): ?array
    {
        $sql = 'SELECT * FROM urls WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
