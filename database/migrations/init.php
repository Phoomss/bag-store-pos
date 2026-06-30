<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Load environmental variables
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

$host = $_ENV['DB_HOST'] ?? 'db';
$port = $_ENV['DB_PORT'] ?? '3306';
$db   = $_ENV['DB_DATABASE'] ?? 'bag_pos';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? 'root';

echo "Initializing Database connection to {$host}:{$port}...\n";

try {
    // Connect to MySQL (without database first, to ensure DB exists)
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database `{$db}` checked/created successfully.\n";

    // Reconnect to target database
    $pdo->exec("USE `{$db}`");

    // Load schema sql
    $schemaFile = __DIR__ . '/../schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found at " . $schemaFile);
    }

    $sql = file_get_contents($schemaFile);

    // Execute queries
    echo "Executing schema migration and seeders...\n";
    $pdo->exec($sql);

    echo "Database migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
