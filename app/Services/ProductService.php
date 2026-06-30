<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\InventoryRepository;
use App\Helpers\Logger;
use Exception;

class ProductService {
    protected ProductRepository $productRepo;
    protected InventoryRepository $inventoryRepo;

    public function __construct() {
        $this->productRepo = new ProductRepository();
        $this->inventoryRepo = new InventoryRepository();
    }

    public function getProducts(array $filters = []): array {
        return $this->productRepo->all($filters);
    }

    public function getProduct(int $id): ?array {
        return $this->productRepo->find($id);
    }

    public function searchProducts(string $query): array {
        return $this->productRepo->all(['search' => $query, 'status' => 'Active']);
    }

    public function getProductByBarcode(string $barcode): ?array {
        return $this->productRepo->findByBarcode($barcode);
    }

    public function createProduct(array $data, array $uploadedImages = []): int {
        // Validate barcode duplicate
        if ($this->productRepo->findByBarcode($data['barcode'])) {
            throw new Exception("Barcode '{$data['barcode']}' already exists in the system.");
        }

        // Validate SKU duplicate
        if ($this->productRepo->findBySku($data['sku'])) {
            throw new Exception("SKU '{$data['sku']}' already exists in the system.");
        }

        $productId = $this->productRepo->create($data);

        if ($productId) {
            Logger::log('Product Creation', "Created product SKU: {$data['sku']} (ID: {$productId})");

            // If initial stock is greater than 0, write stock movement log
            if (!empty($data['stock_quantity']) && $data['stock_quantity'] > 0) {
                $this->inventoryRepo->logMovement([
                    'product_id' => $productId,
                    'type' => 'Adjustment',
                    'reference_id' => null,
                    'quantity' => (int)$data['stock_quantity'],
                    'remaining_stock' => (int)$data['stock_quantity'],
                    'cost_price' => (float)$data['cost_price']
                ]);
            }

            // Handle images upload
            $this->handleImagesUpload($productId, $uploadedImages);
        }

        return $productId;
    }

    public function updateProduct(int $id, array $data, array $uploadedImages = []): bool {
        $product = $this->productRepo->find($id);
        if (!$product) {
            throw new Exception("Product not found.");
        }

        // Validate duplicates excluding current ID
        $byBarcode = $this->productRepo->findByBarcode($data['barcode']);
        if ($byBarcode && $byBarcode['id'] !== $id) {
            throw new Exception("Barcode '{$data['barcode']}' is already assigned to another product.");
        }

        $bySku = $this->productRepo->findBySku($data['sku']);
        if ($bySku && $bySku['id'] !== $id) {
            throw new Exception("SKU '{$data['sku']}' is already assigned to another product.");
        }

        $success = $this->productRepo->update($id, $data);

        if ($success) {
            Logger::log('Product Update', "Updated product SKU: {$data['sku']} (ID: {$id})");
            $this->handleImagesUpload($id, $uploadedImages);
        }

        return $success;
    }

    public function deleteProduct(int $id): bool {
        $product = $this->productRepo->find($id);
        if (!$product) {
            throw new Exception("Product not found.");
        }

        // Delete images files from physical storage
        $images = $this->productRepo->getImages($id);
        foreach ($images as $img) {
            $filePath = dirname(__DIR__, 2) . '/public' . $img['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $success = $this->productRepo->delete($id);
        if ($success) {
            Logger::log('Product Deletion', "Deleted product SKU: {$product['sku']} (ID: {$id})");
        }
        return $success;
    }

    private function handleImagesUpload(int $productId, array $files): void {
        if (empty($files) || !isset($files['name'])) {
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Check if multiple files or single file
        $fileArray = [];
        if (is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $fileArray[] = [
                        'name' => $files['name'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'type' => $files['type'][$i],
                        'size' => $files['size'][$i],
                    ];
                }
            }
        } else {
            if ($files['error'] === UPLOAD_ERR_OK) {
                $fileArray[] = $files;
            }
        }

        $isPrimary = true;
        // Check if there is already a primary image
        $existing = $this->productRepo->getImages($productId);
        foreach ($existing as $img) {
            if ($img['is_primary'] == 1) {
                $isPrimary = false;
                break;
            }
        }

        foreach ($fileArray as $file) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = 'prod_' . $productId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $destPath = $uploadDir . $newFilename;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $dbPath = '/uploads/products/' . $newFilename;
                $this->productRepo->addImage($productId, $dbPath, $isPrimary);
                $isPrimary = false; // only the first new uploaded image becomes primary if none existed
            }
        }
    }

    public function importFromCsv(string $filePath): int {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("CSV File is not readable.");
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle); // Read headers

        $imported = 0;
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($row) < 10) continue; // skip incomplete rows

            // Format: SKU, Barcode, Name, Brand ID, Category ID, Cost Price, Selling Price, Stock Qty, Min Stock, Description
            $data = [
                'sku' => trim($row[0]),
                'barcode' => trim($row[1]),
                'name' => trim($row[2]),
                'brand_id' => !empty($row[3]) ? (int)$row[3] : null,
                'category_id' => !empty($row[4]) ? (int)$row[4] : null,
                'color' => trim($row[5] ?? ''),
                'material' => trim($row[6] ?? ''),
                'size' => trim($row[7] ?? ''),
                'cost_price' => (float)$row[8],
                'selling_price' => (float)$row[9],
                'stock_quantity' => !empty($row[10]) ? (int)$row[10] : 0,
                'min_stock' => !empty($row[11]) ? (int)$row[11] : 5,
                'description' => trim($row[12] ?? ''),
                'status' => 'Active'
            ];

            try {
                $this->createProduct($data);
                $imported++;
            } catch (Exception $e) {
                // Log and continue, or fail depending on preference
                error_log("CSV Import failed at line {$rowNum}: " . $e->getMessage());
            }
        }

        fclose($handle);
        return $imported;
    }
}
