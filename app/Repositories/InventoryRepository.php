<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class InventoryRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getMovements(array $filters = []): array {
        $sql = "SELECT sm.*, p.name as product_name, p.sku, p.barcode 
                FROM stock_movements sm
                JOIN products p ON sm.product_id = p.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['product_id'])) {
            $sql .= " AND sm.product_id = ?";
            $params[] = $filters['product_id'];
        }

        if (!empty($filters['type'])) {
            $sql .= " AND sm.type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(sm.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(sm.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY sm.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAdjustments(): array {
        $stmt = $this->db->query("SELECT ia.*, p.name as product_name, p.sku, p.barcode, u.name as user_name 
                                  FROM inventory_adjustments ia
                                  JOIN products p ON ia.product_id = p.id
                                  JOIN users u ON ia.user_id = u.id
                                  ORDER BY ia.id DESC");
        return $stmt->fetchAll();
    }

    public function addAdjustment(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO inventory_adjustments (type, product_id, quantity, user_id, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['type'],
            $data['product_id'],
            $data['quantity'],
            $data['user_id'],
            $data['reason']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function logMovement(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO stock_movements (product_id, type, reference_id, quantity, remaining_stock, cost_price) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['product_id'],
            $data['type'],
            $data['reference_id'] ?? null,
            $data['quantity'],
            $data['remaining_stock'],
            $data['cost_price']
        ]);
    }
}
