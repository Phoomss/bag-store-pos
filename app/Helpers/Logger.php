<?php

namespace App\Helpers;

use App\Core\Database;
use PDO;

class Logger {
    public static function log(string $action, string $description, ?int $userId = null): void {
        try {
            $db = Database::getConnection();
            $userId = $userId ?? Session::get('user_id');
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $description, $ip, $ua]);
        } catch (\Exception $e) {
            // Silently fail or log to file to prevent halting request on logger failure
            error_log("Failed to log audit: " . $e->getMessage());
        }
    }

    public static function logLogin(string $email, string $status, ?int $userId = null): void {
        try {
            $db = Database::getConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $stmt = $db->prepare("INSERT INTO login_history (user_id, email, ip_address, user_agent, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $email, $ip, $ua, $status]);
        } catch (\Exception $e) {
            error_log("Failed to log login: " . $e->getMessage());
        }
    }
}
