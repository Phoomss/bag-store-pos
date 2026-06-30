<?php

namespace App\Services;

use App\Repositories\PurchaseRepository;
use App\Repositories\ProductRepository;
use App\Repositories\InventoryRepository;
use App\Core\Database;
use App\Helpers\Logger;
use App\Helpers\Session;
use Exception;

class PurchaseService {
    protected PurchaseRepository $purchaseRepo;
    protected ProductRepository $productRepo;
    protected InventoryRepository $inventoryRepo;

    public function __construct() {
        $this->purchaseRepo = new PurchaseRepository();
        $this->productRepo = new ProductRepository();
        $this->inventoryRepo = new InventoryRepository();
    }

    public function getPurchases(): array {
        return $this->purchaseRepo->all();
    }

    public function getPurchaseDetails(int $id): ?array {
        return $this->purchaseRepo->find($id);
    }

    public function createPurchase(array $data): int {
        Database::beginTransaction();
        try {
            $data['user_id'] = Session::get('user_id');
            if (empty($data['purchase_order_no'])) {
                $data['purchase_order_no'] = $this->purchaseRepo->generateOrderNumber();
            }
            
            // 1. Create purchase order
            $purchaseId = $this->purchaseRepo->create($data);
            
            // 2. Add purchase items
            foreach ($data['items'] as $item) {
                $item['purchase_id'] = $purchaseId;
                
                // Set default received quantity based on initial status
                if (($data['status'] ?? 'Ordered') === 'Received') {
                    $item['received_quantity'] = $item['quantity'];
                } else {
                    $item['received_quantity'] = 0;
                }
                
                $this->purchaseRepo->addItem($item);

                // 3. If received immediately, update product stock & cost price and log movement
                if (($data['status'] ?? 'Ordered') === 'Received') {
                    $this->receiveStockItem($purchaseId, $item['product_id'], $item['quantity'], $item['cost_price']);
                }
            }

            // 4. Add initial payment if provided
            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {
                $this->purchaseRepo->addPayment([
                    'purchase_id' => $purchaseId,
                    'amount' => (float)$data['paid_amount'],
                    'payment_method' => $data['payment_method'] ?? 'Cash',
                    'payment_date' => $data['order_date'],
                    'reference_no' => $data['reference_no'] ?? null
                ]);
            }

            Database::commit();
            Logger::log('Purchase Creation', "Created purchase order: {$data['purchase_order_no']} (ID: {$purchaseId})");
            return $purchaseId;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function addPayment(int $purchaseId, array $paymentData): bool {
        Database::beginTransaction();
        try {
            $purchase = $this->purchaseRepo->find($purchaseId);
            if (!$purchase) {
                throw new Exception("Purchase record not found.");
            }

            $success = $this->purchaseRepo->addPayment($paymentData);
            if ($success) {
                Logger::log('Purchase Payment', "Paid {$paymentData['amount']} to Supplier for PO ID: {$purchaseId}");
            }

            Database::commit();
            return $success;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $purchaseId, string $status, ?string $invoiceNo = null): bool {
        Database::beginTransaction();
        try {
            $purchase = $this->purchaseRepo->find($purchaseId);
            if (!$purchase) {
                throw new Exception("Purchase record not found.");
            }

            if ($purchase['status'] === 'Received') {
                throw new Exception("This purchase has already been received. Status cannot be modified.");
            }

            $receivedDate = ($status === 'Received') ? date('Y-m-d') : null;
            $success = $this->purchaseRepo->updateStatus($purchaseId, $status, $receivedDate);

            if ($success) {
                if ($invoiceNo) {
                    $stmt = Database::getConnection()->prepare("UPDATE purchases SET invoice_no = ? WHERE id = ?");
                    $stmt->execute([$invoiceNo, $purchaseId]);
                }

                // If status changed to Received, receive stock and log movements
                if ($status === 'Received') {
                    $items = $this->purchaseRepo->getItems($purchaseId);
                    foreach ($items as $item) {
                        // Update item received quantity in DB
                        $this->purchaseRepo->updateItemReceivedQuantity($item['id'], $item['quantity']);
                        
                        // Increase product stock and log movement
                        $this->receiveStockItem($purchaseId, $item['product_id'], $item['quantity'], $item['cost_price']);
                    }
                }
                
                Logger::log('Purchase Update', "Updated PO {$purchase['purchase_order_no']} status to {$status}");
            }

            Database::commit();
            return $success;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    private function receiveStockItem(int $purchaseId, int $productId, int $qty, float $costPrice): void {
        // Update product stock and purchase cost price
        $this->productRepo->updateStock($productId, $qty);
        
        // Update cost price in products catalog to reflect latest cost
        $stmt = Database::getConnection()->prepare("UPDATE products SET cost_price = ? WHERE id = ?");
        $stmt->execute([$costPrice, $productId]);

        // Fetch new product stock to log remaining
        $product = $this->productRepo->find($productId);
        $newStock = $product['stock_quantity'] ?? 0;

        // Log movement
        $this->inventoryRepo->logMovement([
            'product_id' => $productId,
            'type' => 'Purchase',
            'reference_id' => $purchaseId,
            'quantity' => $qty,
            'remaining_stock' => $newStock,
            'cost_price' => $costPrice
        ]);
    }
}
