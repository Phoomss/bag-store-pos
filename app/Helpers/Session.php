<?php

namespace App\Helpers;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_use_only_cookies', 1);
            
            // Secure cookie in HTTPS if available
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }

            session_start();
        }

        // Generate CSRF token if not set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Handle Session Timeout (120 minutes by default)
        $lifetime = ($_ENV['SESSION_LIFETIME'] ?? 120) * 60;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $lifetime)) {
            self::destroy();
        }
        $_SESSION['last_activity'] = time();
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value = null): mixed {
        if ($value !== null) {
            $_SESSION['flash'][$key] = $value;
            return null;
        }

        $flash = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $flash;
    }

    public static function hasFlash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }

    public static function csrfToken(): string {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function validateCsrf(?string $token): bool {
        if (!$token) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
        }
    }

    public static function setUser(array $user): void {
        self::set('user_id', $user['id']);
        self::set('user_name', $user['name']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role_name']);
        self::set('user_permissions', $user['permissions'] ?? []);
    }

    public static function isLoggedIn(): bool {
        return self::get('user_id') !== null;
    }

    public static function hasPermission(string $permission): bool {
        $role = self::get('user_role');
        if ($role === 'Owner') {
            return true; // Owner has all permissions
        }
        $perms = self::get('user_permissions', []);
        return in_array($permission, $perms);
    }

    public static function checkRole(array $allowedRoles): bool {
        $role = self::get('user_role');
        if ($role === 'Owner') {
            return true; // Owner is always allowed
        }
        return in_array($role, $allowedRoles);
    }
}
