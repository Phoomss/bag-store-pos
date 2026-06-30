<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PurchaseRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT p.*, s.name as supplier_name, u.name as user_name 
                                  FROM purchases p
                                  JOIN suppliers s ON p.supplier_id = s.id
                                  JOIN users u ON p.user_id = u.id
                                  ORDER BY p.id DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT p.*, s.name as supplier_name, s.phone as supplier_phone, s.address as supplier_address, u.name as user_name 
                                    FROM purchases p
                                    JOIN suppliers s ON p.supplier_id = s.id
                                    JOIN users u ON p.user_id = u.id
                                    WHERE p.id = ?");
        $stmt->execute([$id]);
        $purchase = $stmt->fetch();
        if ($purchase) {
            $purchase['items'] = $this->getItems($id);
            $purchase['payments'] = $this->getPayments($id);
        }
        return $purchase ?: null;
    }

    public function generateOrderNumber(): string {
        $stmt = $this->db->query("SELECT MAX(id) as max_id FROM purchases");
        $row = $stmt->fetch();
        $nextId = ($row['max_id'] ?? 0) + 1;
        return 'PO-' . date('Ymd') . '-' . str_pad((string)$nextId, 4, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int {
        if (empty($data['purchase_order_no'])) {
            $data['purchase_order_no'] = $this->generateOrderNumber();
        }

        $stmt = $this->db->prepare("INSERT INTO purchases 
            (purchase_order_no, supplier_id, user_id, status, total_amount, paid_amount, balance_amount, payment_status, order_date, received_date, invoice_no) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data['purchase_order_no'],
            $data['supplier_id'],
            $data['user_id'],
            $data['status'] ?? 'Ordered',
            $data['total_amount'],
            $data['paid_amount'] ?? 0.00,
            $data['total_amount'] - ($data['paid_amount'] ?? 0.00),
            $data['payment_status'] ?? 'Unpaid',
            $data['order_date'],
            $data['received_date'] ?? null,
            $data['invoice_no'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $purchaseId, string $status, ?string $receivedDate = null): bool {
        $sql = "UPDATE purchases SET status = ?";
        $params = [$status];
        if ($receivedDate) {
            $sql .= ", received_date = ?";
            $params[] = $receivedDate;
        }
        $sql .= " WHERE id = ?";
        $params[] = $purchaseId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Purchase Items
    public function addItem(array $itemData): bool {
        $stmt = $this->db->prepare("INSERT INTO purchase_items 
            (purchase_id, product_id, cost_price, quantity, received_quantity, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $itemData['purchase_id'],
            $itemData['product_id'],
            $itemData['cost_price'],
            $itemData['quantity'],
            $itemData['received_quantity'] ?? 0,
            $itemData['subtotal']
        ]);
    }

    public function updateItemReceivedQuantity(int $itemId, int $receivedQty): bool {
        $stmt = $this->db->prepare("UPDATE purchase_items SET received_quantity = ? WHERE id = ?");
        return $stmt->execute([$receivedQty, $itemId]);
    }

    public function getItems(int $purchaseId): array {
        $stmt = $this->db->prepare("SELECT pi.*, p.name as product_name, p.sku, p.barcode 
                                    FROM purchase_items pi
                                    JOIN products p ON pi.product_id = p.id
                                    WHERE pi.purchase_id = ?");
        $stmt->execute([$purchaseId]);
        return $stmt->fetchAll();
    }

    // Payments
    public function addPayment(array $paymentData): bool {
        $stmt = $this->db->prepare("INSERT INTO purchase_payments 
            (purchase_id, amount, payment_method, payment_date, reference_no) 
            VALUES (?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $paymentData['purchase_id'],
            $paymentData['amount'],
            $paymentData['payment_method'],
            $paymentData['payment_date'],
            $paymentData['reference_no'] ?? null
        ]);

        if ($success) {
            // Update purchase paid_amount, balance_amount, and payment_status
            $purchase = $this->find($paymentData['purchase_id']);
            if ($purchase) {
                $newPaid = $purchase['paid_amount'] + $paymentData['amount'];
                $newBalance = max(0.00, $purchase['total_amount'] - $newPaid);
                
                $paymentStatus = 'Unpaid';
                if ($newPaid >= $purchase['total_amount']) {
                    $paymentStatus = 'Paid';
                } elseif ($newPaid > 0) {
                    $paymentStatus = 'Partial';
                }

                $stmt = $this->db->prepare("UPDATE purchases SET paid_amount = ?, balance_amount = ?, payment_status = ? WHERE id = ?");
                $stmt->execute([$newPaid, $newBalance, $paymentStatus, $purchase['id']]);
            }
        }

        return $success;
    }

    public function getPayments(int $purchaseId): array {
        $stmt = $this->db->prepare("SELECT * FROM purchase_payments WHERE purchase_id = ? ORDER BY payment_date DESC, id DESC");
        $stmt->execute([$purchaseId]);
        return $stmt->fetchAll();
    }
}
