<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT u.*, r.name as role_name 
                                  FROM users u
                                  JOIN roles r ON u.role_id = r.id
                                  ORDER BY u.id ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name 
                                    FROM users u
                                    JOIN roles r ON u.role_id = r.id
                                    WHERE u.id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user) {
            $user['permissions'] = $this->getPermissions($user['role_id']);
        }
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name 
                                    FROM users u
                                    JOIN roles r ON u.role_id = r.id
                                    WHERE u.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $user['permissions'] = $this->getPermissions($user['role_id']);
        }
        return $user ?: null;
    }

    public function getPermissions(int $roleId): array {
        $stmt = $this->db->prepare("SELECT p.name 
                                    FROM permissions p
                                    JOIN role_permissions rp ON p.id = rp.permission_id
                                    WHERE rp.role_id = ?");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRoles(): array {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role_id'],
            $data['status'] ?? 'Active'
        ]);
    }

    public function update(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, password = ?, role_id = ?, status = ? WHERE id = ?");
            return $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role_id'],
                $data['status'],
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role_id = ?, status = ? WHERE id = ?");
            return $stmt->execute([
                $data['name'],
                $data['email'],
                $data['role_id'],
                $data['status'],
                $id
            ]);
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Login and Audit logs queries
    public function getLoginHistory(int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT lh.*, u.name as user_name 
                                    FROM login_history lh
                                    LEFT JOIN users u ON lh.user_id = u.id
                                    ORDER BY lh.login_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAuditLogs(int $limit = 100): array {
        $stmt = $this->db->prepare("SELECT al.*, u.name as user_name 
                                    FROM audit_logs al
                                    LEFT JOIN users u ON al.user_id = u.id
                                    ORDER BY al.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
