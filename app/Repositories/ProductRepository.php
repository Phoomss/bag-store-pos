<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProductRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all(array $filters = []): array {
        $sql = "SELECT p.*, b.name as brand_name, c.name as category_name, 
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $filters['brand_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $sql .= " AND p.stock_quantity <= p.min_stock AND p.stock_quantity > 0";
            } elseif ($filters['stock_status'] === 'out') {
                $sql .= " AND p.stock_quantity <= 0";
            }
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT p.*, b.name as brand_name, c.name as category_name 
                                    FROM products p 
                                    LEFT JOIN brands b ON p.brand_id = b.id 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    WHERE p.id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            $product['images'] = $this->getImages($id);
        }
        return $product ?: null;
    }

    public function findByBarcode(string $barcode): ?array {
        $stmt = $this->db->prepare("SELECT p.*, 
                                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image 
                                    FROM products p WHERE p.barcode = ? AND p.status = 'Active'");
        $stmt->execute([$barcode]);
        return $stmt->fetch() ?: null;
    }

    public function findBySku(string $sku): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE sku = ?");
        $stmt->execute([$sku]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO products 
            (sku, barcode, name, brand_id, category_id, color, material, size, cost_price, selling_price, promotion_price, stock_quantity, min_stock, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data['sku'],
            $data['barcode'],
            $data['name'],
            $data['brand_id'] ?: null,
            $data['category_id'] ?: null,
            $data['color'] ?? null,
            $data['material'] ?? null,
            $data['size'] ?? null,
            $data['cost_price'] ?? 0.00,
            $data['selling_price'] ?? 0.00,
            $data['promotion_price'] ?: null,
            $data['stock_quantity'] ?? 0,
            $data['min_stock'] ?? 5,
            $data['description'] ?? null,
            $data['status'] ?? 'Active'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE products SET 
            sku = ?, barcode = ?, name = ?, brand_id = ?, category_id = ?, color = ?, material = ?, size = ?, 
            cost_price = ?, selling_price = ?, promotion_price = ?, min_stock = ?, description = ?, status = ? 
            WHERE id = ?");
        
        return $stmt->execute([
            $data['sku'],
            $data['barcode'],
            $data['name'],
            $data['brand_id'] ?: null,
            $data['category_id'] ?: null,
            $data['color'] ?? null,
            $data['material'] ?? null,
            $data['size'] ?? null,
            $data['cost_price'],
            $data['selling_price'],
            $data['promotion_price'] ?: null,
            $data['min_stock'],
            $data['description'] ?? null,
            $data['status'],
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateStock(int $productId, int $quantityChange): bool {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        return $stmt->execute([$quantityChange, $productId]);
    }

    // Images
    public function getImages(int $productId): array {
        $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, id ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addImage(int $productId, string $path, bool $isPrimary = false): bool {
        if ($isPrimary) {
            // Reset existing primary images
            $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
            $stmt->execute([$productId]);
        }
        
        $stmt = $this->db->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)");
        return $stmt->execute([$productId, $path, $isPrimary ? 1 : 0]);
    }

    public function clearImages(int $productId): bool {
        $stmt = $this->db->prepare("DELETE FROM product_images WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    public function getLowStockProducts(): array {
        $stmt = $this->db->query("SELECT p.*, b.name as brand_name, c.name as category_name 
                                  FROM products p 
                                  LEFT JOIN brands b ON p.brand_id = b.id 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  WHERE p.stock_quantity <= p.min_stock AND p.stock_quantity > 0 AND p.status = 'Active'");
        return $stmt->fetchAll();
    }

    public function getOutOfStockProducts(): array {
        $stmt = $this->db->query("SELECT p.*, b.name as brand_name, c.name as category_name 
                                  FROM products p 
                                  LEFT JOIN brands b ON p.brand_id = b.id 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  WHERE p.stock_quantity <= 0 AND p.status = 'Active'");
        return $stmt->fetchAll();
    }
}
