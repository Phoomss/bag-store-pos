<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class BrandRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM brands ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO brands (name, description) VALUES (?, ?)");
        return $stmt->execute([$data['name'], $data['description'] ?? null]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE brands SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['description'] ?? null, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM brands WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
