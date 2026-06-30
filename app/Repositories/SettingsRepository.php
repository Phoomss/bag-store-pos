<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SettingsRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key_name']] = $row['value_data'];
        }
        return $settings;
    }

    public function get(string $key, mixed $default = null): mixed {
        $stmt = $this->db->prepare("SELECT value_data FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['value_data'] : $default;
    }

    public function set(string $key, ?string $value): bool {
        $stmt = $this->db->prepare("INSERT INTO settings (key_name, value_data) 
                                    VALUES (?, ?) 
                                    ON DUPLICATE KEY UPDATE value_data = ?, updated_at = CURRENT_TIMESTAMP");
        return $stmt->execute([$key, $value, $value]);
    }

    public function setMultiple(array $data): bool {
        foreach ($data as $key => $value) {
            $this->set($key, $value !== null ? (string)$value : null);
        }
        return true;
    }
}
