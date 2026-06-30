<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ExpenseRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(array $filters = []): array {
        $sql = "SELECT e.*, u.name as user_name 
                FROM expenses e
                JOIN users u ON e.user_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND e.category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND e.expense_date >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND e.expense_date <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY e.expense_date DESC, e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM expenses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO expenses (category, amount, description, expense_date, user_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['category'],
            $data['amount'],
            $data['description'] ?? null,
            $data['expense_date'],
            $data['user_id']
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCategoryTotals(string $startDate, string $endDate): array {
        $stmt = $this->db->prepare("SELECT category, SUM(amount) as total 
                                    FROM expenses 
                                    WHERE expense_date >= ? AND expense_date <= ? 
                                    GROUP BY category");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
