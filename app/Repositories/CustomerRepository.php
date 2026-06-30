<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CustomerRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(string $search = ''): array {
        if (!empty($search)) {
            $stmt = $this->db->prepare("SELECT * FROM customers 
                                        WHERE name LIKE ? OR phone LIKE ? OR customer_code LIKE ? 
                                        ORDER BY name ASC");
            $term = "%{$search}%";
            $stmt->execute([$term, $term, $term]);
        } else {
            $stmt = $this->db->query("SELECT * FROM customers ORDER BY name ASC");
        }
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByPhone(string $phone): ?array {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE phone = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch() ?: null;
    }

    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE customer_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public function generateCustomerCode(): string {
        $stmt = $this->db->query("SELECT MAX(id) as max_id FROM customers");
        $row = $stmt->fetch();
        $nextId = ($row['max_id'] ?? 0) + 1;
        return 'CUST' . str_pad((string)$nextId, 5, '0', STR_PAD_LEFT);
    }

    public function create(array $data): bool {
        if (empty($data['customer_code'])) {
            $data['customer_code'] = $this->generateCustomerCode();
        }

        $stmt = $this->db->prepare("INSERT INTO customers 
            (customer_code, name, phone, email, birthday, gender, address, reward_points, membership_level) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $data['customer_code'],
            $data['name'],
            $data['phone'],
            $data['email'] ?: null,
            $data['birthday'] ?: null,
            $data['gender'] ?: null,
            $data['address'] ?? null,
            $data['reward_points'] ?? 0,
            $data['membership_level'] ?? 'Bronze'
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE customers SET 
            name = ?, phone = ?, email = ?, birthday = ?, gender = ?, address = ?, reward_points = ?, membership_level = ? 
            WHERE id = ?");
        
        return $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['email'] ?: null,
            $data['birthday'] ?: null,
            $data['gender'] ?: null,
            $data['address'] ?? null,
            $data['reward_points'],
            $data['membership_level'],
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function addPoints(int $customerId, int $points): bool {
        $stmt = $this->db->prepare("UPDATE customers SET reward_points = reward_points + ? WHERE id = ?");
        $stmt->execute([$points, $customerId]);

        // Auto-update membership level based on points balance
        $customer = $this->find($customerId);
        if ($customer) {
            $pts = $customer['reward_points'];
            $newLevel = 'Bronze';
            if ($pts >= 1000) {
                $newLevel = 'Platinum';
            } elseif ($pts >= 500) {
                $newLevel = 'Gold';
            } elseif ($pts >= 200) {
                $newLevel = 'Silver';
            }

            if ($newLevel !== $customer['membership_level']) {
                $stmt = $this->db->prepare("UPDATE customers SET membership_level = ? WHERE id = ?");
                $stmt->execute([$newLevel, $customerId]);
            }
        }

        return true;
    }

    public function deductPoints(int $customerId, int $points): bool {
        $stmt = $this->db->prepare("UPDATE customers SET reward_points = GREATEST(0, reward_points - ?) WHERE id = ?");
        return $stmt->execute([$points, $customerId]);
    }
}
