<?php

namespace App\Services;

use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use App\Core\Database;
use App\Helpers\Logger;
use App\Helpers\Session;
use Exception;

class InventoryService {
    protected InventoryRepository $inventoryRepo;
    protected ProductRepository $productRepo;

    public function __construct() {
        $this->inventoryRepo = new InventoryRepository();
        $this->productRepo = new ProductRepository();
    }

    public function getAdjustments(): array {
        return $this->inventoryRepo->getAdjustments();
    }

    public function getMovements(array $filters = []): array {
        return $this->inventoryRepo->getMovements($filters);
    }

    public function adjustStock(array $data): bool {
        Database::beginTransaction();
        try {
            $data['user_id'] = Session::get('user_id');

            $product = $this->productRepo->find($data['product_id']);
            if (!$product) {
                throw new Exception("Product not found.");
            }

            // Check if reducing stock and if enough stock exists
            $qtyChange = (int)$data['quantity'];
            if ($qtyChange < 0 && $product['stock_quantity'] < abs($qtyChange)) {
                throw new Exception("Cannot deduct stock. Available quantity is {$product['stock_quantity']}, requested deduction: " . abs($qtyChange));
            }

            // 1. Log manual adjustment
            $adjustmentId = $this->inventoryRepo->addAdjustment($data);

            // 2. Update stock quantity in products catalog
            $this->productRepo->updateStock($data['product_id'], $qtyChange);

            // 3. Fetch remaining stock to log in movements
            $updatedProduct = $this->productRepo->find($data['product_id']);
            $remaining = $updatedProduct['stock_quantity'] ?? 0;

            // 4. Log detailed movement
            // Mapping adjustment types to movement types
            $movType = 'Adjustment';
            if ($data['type'] === 'Transfer') $movType = 'Transfer';
            if ($data['type'] === 'Damaged') $movType = 'Damage';
            if ($data['type'] === 'Lost') $movType = 'Lost';

            $this->inventoryRepo->logMovement([
                'product_id' => $data['product_id'],
                'type' => $movType,
                'reference_id' => $adjustmentId,
                'quantity' => $qtyChange,
                'remaining_stock' => $remaining,
                'cost_price' => $product['cost_price']
            ]);

            Database::commit();
            Logger::log('Stock Adjustment', "Adjusted stock for product: {$product['name']} (SKU: {$product['sku']}). Change: {$qtyChange}");
            return true;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }
}
