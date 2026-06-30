<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SupplierRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        // Fetch all suppliers and calculate outstanding balances
        $sql = "SELECT s.*, 
                COALESCE(SUM(p.total_amount), 0.00) as total_ordered,
                COALESCE(SUM(p.paid_amount), 0.00) as total_paid,
                (COALESCE(SUM(p.total_amount), 0.00) - COALESCE(SUM(p.paid_amount), 0.00)) as outstanding_balance
                FROM suppliers s
                LEFT JOIN purchases p ON s.id = p.supplier_id AND p.status != 'Cancelled'
                GROUP BY s.id
                ORDER BY s.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO suppliers (name, contact_name, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE suppliers SET name = ?, contact_name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM suppliers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPurchaseHistory(int $supplierId): array {
        $stmt = $this->db->prepare("SELECT p.*, u.name as user_name 
                                    FROM purchases p
                                    JOIN users u ON p.user_id = u.id
                                    WHERE p.supplier_id = ?
                                    ORDER BY p.order_date DESC");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    public function getPaymentHistory(int $supplierId): array {
        $stmt = $this->db->prepare("SELECT pp.*, p.purchase_order_no 
                                    FROM purchase_payments pp
                                    JOIN purchases p ON pp.purchase_id = p.id
                                    WHERE p.supplier_id = ?
                                    ORDER BY pp.payment_date DESC");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }
}
