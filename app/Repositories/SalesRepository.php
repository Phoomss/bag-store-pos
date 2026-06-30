<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SalesRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(array $filters = []): array {
        $sql = "SELECT s.*, c.name as customer_name, u.name as cashier_name 
                FROM sales s
                LEFT JOIN customers c ON s.customer_id = c.id
                JOIN users u ON s.user_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND s.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(s.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(s.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (s.invoice_no LIKE ? OR c.name LIKE ?)";
            $term = "%{$filters['search']}%";
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY s.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT s.*, c.name as customer_name, c.phone as customer_phone, c.customer_code, u.name as cashier_name 
                                    FROM sales s
                                    LEFT JOIN customers c ON s.customer_id = c.id
                                    JOIN users u ON s.user_id = u.id
                                    WHERE s.id = ?");
        $stmt->execute([$id]);
        $sale = $stmt->fetch();
        if ($sale) {
            $sale['items'] = $this->getItems($id);
            $sale['payments'] = $this->getPayments($id);
        }
        return $sale ?: null;
    }

    public function findByInvoice(string $invoiceNo): ?array {
        $stmt = $this->db->prepare("SELECT id FROM sales WHERE invoice_no = ?");
        $stmt->execute([$invoiceNo]);
        $row = $stmt->fetch();
        return $row ? $this->find($row['id']) : null;
    }

    public function generateInvoiceNumber(): string {
        $stmt = $this->db->query("SELECT MAX(id) as max_id FROM sales");
        $row = $stmt->fetch();
        $nextId = ($row['max_id'] ?? 0) + 1;
        return 'INV-' . date('Ymd') . '-' . str_pad((string)$nextId, 5, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int {
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = $this->generateInvoiceNumber();
        }

        $stmt = $this->db->prepare("INSERT INTO sales 
            (invoice_no, customer_id, user_id, subtotal, discount_amount, coupon_code, vat_amount, shipping_amount, total_amount, paid_amount, change_amount, payment_method, payment_status, status, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data['invoice_no'],
            $data['customer_id'] ?: null,
            $data['user_id'],
            $data['subtotal'],
            $data['discount_amount'] ?? 0.00,
            $data['coupon_code'] ?? null,
            $data['vat_amount'] ?? 0.00,
            $data['shipping_amount'] ?? 0.00,
            $data['total_amount'],
            $data['paid_amount'] ?? 0.00,
            $data['change_amount'] ?? 0.00,
            $data['payment_method'],
            $data['payment_status'] ?? 'Paid',
            $data['status'] ?? 'Completed',
            $data['notes'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $paymentStatus = null): bool {
        $sql = "UPDATE sales SET status = ?";
        $params = [$status];
        if ($paymentStatus) {
            $sql .= ", payment_status = ?";
            $params[] = $paymentStatus;
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Items
    public function addItem(array $itemData): bool {
        $stmt = $this->db->prepare("INSERT INTO sale_items 
            (sale_id, product_id, selling_price, quantity, discount_amount, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $itemData['sale_id'],
            $itemData['product_id'],
            $itemData['selling_price'],
            $itemData['quantity'],
            $itemData['discount_amount'] ?? 0.00,
            $itemData['subtotal']
        ]);
    }

    public function getItems(int $saleId): array {
        $stmt = $this->db->prepare("SELECT si.*, p.name as product_name, p.sku, p.barcode 
                                    FROM sale_items si
                                    JOIN products p ON si.product_id = p.id
                                    WHERE si.sale_id = ?");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    // Payments (for mixed payment breakdown)
    public function addPayment(array $paymentData): bool {
        $stmt = $this->db->prepare("INSERT INTO sale_payments (sale_id, payment_method, amount, reference_no) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $paymentData['sale_id'],
            $paymentData['payment_method'],
            $paymentData['amount'],
            $paymentData['reference_no'] ?? null
        ]);
    }

    public function getPayments(int $saleId): array {
        $stmt = $this->db->prepare("SELECT * FROM sale_payments WHERE sale_id = ? ORDER BY id ASC");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    // Hold / Resume
    public function getHeldSales(int $userId): array {
        $stmt = $this->db->prepare("SELECT s.*, c.name as customer_name 
                                    FROM sales s
                                    LEFT JOIN customers c ON s.customer_id = c.id
                                    WHERE s.status = 'Held' AND s.user_id = ?
                                    ORDER BY s.id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function deleteHeldSale(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM sales WHERE id = ? AND status = 'Held'");
        return $stmt->execute([$id]);
    }
}
