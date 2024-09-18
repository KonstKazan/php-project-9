<?php

namespace PageAnalyzer;

use PDO;

class UrlChecks
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $id, int $status, string $h, string $title, string $description, string $create): int
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

    public function getAll(int $id): ?array
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
