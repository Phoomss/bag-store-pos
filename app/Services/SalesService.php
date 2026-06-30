<?php

namespace App\Services;

use App\Repositories\SalesRepository;
use App\Repositories\ProductRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\InventoryRepository;
use App\Core\Database;
use App\Helpers\Logger;
use App\Helpers\Session;
use Exception;

class SalesService {
    protected SalesRepository $salesRepo;
    protected ProductRepository $productRepo;
    protected CustomerRepository $customerRepo;
    protected InventoryRepository $inventoryRepo;

    public function __construct() {
        $this->salesRepo = new SalesRepository();
        $this->productRepo = new ProductRepository();
        $this->customerRepo = new CustomerRepository();
        $this->inventoryRepo = new InventoryRepository();
    }

    public function getSales(array $filters = []): array {
        return $this->salesRepo->all($filters);
    }

    public function getSaleDetails(int $id): ?array {
        return $this->salesRepo->find($id);
    }

    public function checkout(array $data): int {
        Database::beginTransaction();
        try {
            $data['user_id'] = Session::get('user_id');
            if (empty($data['invoice_no'])) {
                $data['invoice_no'] = $this->salesRepo->generateInvoiceNumber();
            }

            // 1. Validate stock levels for all products
            foreach ($data['items'] as $item) {
                $product = $this->productRepo->find($item['product_id']);
                if (!$product) {
                    throw new Exception("Product ID {$item['product_id']} not found.");
                }

                // Skip checking stock if status is Held
                if (($data['status'] ?? 'Completed') === 'Completed' && $product['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for product '{$product['name']}'. Available: {$product['stock_quantity']}, Requested: {$item['quantity']}");
                }
            }

            // 2. Create the sale entry
            $saleId = $this->salesRepo->create($data);

            // 3. Save items and deduct stock if status is Completed
            foreach ($data['items'] as $item) {
                $item['sale_id'] = $saleId;
                $this->salesRepo->addItem($item);

                if (($data['status'] ?? 'Completed') === 'Completed') {
                    // Deduct inventory
                    $this->productRepo->updateStock($item['product_id'], -$item['quantity']);

                    $product = $this->productRepo->find($item['product_id']);
                    $remaining = $product['stock_quantity'] ?? 0;

                    // Log stock movement
                    $this->inventoryRepo->logMovement([
                        'product_id' => $item['product_id'],
                        'type' => 'Sale',
                        'reference_id' => $saleId,
                        'quantity' => -$item['quantity'],
                        'remaining_stock' => $remaining,
                        'cost_price' => $product['cost_price']
                    ]);
                }
            }

            // 4. Record payments (only for Completed sales)
            if (($data['status'] ?? 'Completed') === 'Completed') {
                if (!empty($data['payments'])) {
                    foreach ($data['payments'] as $payment) {
                        $payment['sale_id'] = $saleId;
                        $this->salesRepo->addPayment($payment);
                    }
                } else {
                    // If single payment type, log standard payment record
                    $this->salesRepo->addPayment([
                        'sale_id' => $saleId,
                        'payment_method' => $data['payment_method'] ?? 'Cash',
                        'amount' => $data['paid_amount'] ?? 0.00,
                        'reference_no' => $data['reference_no'] ?? null
                    ]);
                }
            }

            // 5. Customer rewards point handling
            if (!empty($data['customer_id']) && $data['customer_id'] != 1) { // 1 is Walk-in customer
                // Points Earned: 1 point for every 50 Baht spent (using total_amount)
                $pointsEarned = floor($data['total_amount'] / 50);
                if ($pointsEarned > 0 && ($data['status'] ?? 'Completed') === 'Completed') {
                    $this->customerRepo->addPoints($data['customer_id'], $pointsEarned);
                }

                // Points Redeemed (if any points discount was used, deduct them)
                if (!empty($data['points_redeemed']) && $data['points_redeemed'] > 0) {
                    $this->customerRepo->deductPoints($data['customer_id'], (int)$data['points_redeemed']);
                }
            }

            Database::commit();
            
            $logAction = (($data['status'] ?? 'Completed') === 'Held') ? 'Hold Sale' : 'POS Checkout';
            Logger::log($logAction, "Invoice {$data['invoice_no']} processed. Total: {$data['total_amount']}");
            
            return $saleId;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function refund(int $saleId): bool {
        Database::beginTransaction();
        try {
            $sale = $this->salesRepo->find($saleId);
            if (!$sale) {
                throw new Exception("Sale record not found.");
            }

            if ($sale['payment_status'] === 'Refunded') {
                throw new Exception("This sale invoice is already refunded.");
            }

            // 1. Update status
            $this->salesRepo->updateStatus($saleId, 'Cancelled', 'Refunded');

            // 2. Put items back to stock and log movements
            $items = $this->salesRepo->getItems($saleId);
            foreach ($items as $item) {
                // Put back inventory
                $this->productRepo->updateStock($item['product_id'], $item['quantity']);

                $product = $this->productRepo->find($item['product_id']);
                $remaining = $product['stock_quantity'] ?? 0;

                // Log movement
                $this->inventoryRepo->logMovement([
                    'product_id' => $item['product_id'],
                    'type' => 'Return',
                    'reference_id' => $saleId,
                    'quantity' => $item['quantity'],
                    'remaining_stock' => $remaining,
                    'cost_price' => $product['cost_price']
                ]);
            }

            // 3. Deduct points earned from customer profile
            if (!empty($sale['customer_id']) && $sale['customer_id'] != 1) {
                $pointsEarned = floor($sale['total_amount'] / 50);
                if ($pointsEarned > 0) {
                    $this->customerRepo->deductPoints($sale['customer_id'], $pointsEarned);
                }
            }

            Database::commit();
            Logger::log('Sale Refund', "Refunded invoice {$sale['invoice_no']} (ID: {$saleId})");
            return true;
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    public function getHeldSales(): array {
        $userId = Session::get('user_id');
        return $this->salesRepo->getHeldSales($userId);
    }

    public function resumeSale(int $heldSaleId): array {
        $sale = $this->salesRepo->find($heldSaleId);
        if (!$sale || $sale['status'] !== 'Held') {
            throw new Exception("Held sale not found or already completed.");
        }

        // Return details and delete the held sale from database so it can be re-submitted or completed
        $this->salesRepo->deleteHeldSale($heldSaleId);
        Logger::log('Resume Sale', "Resumed held sale invoice: {$sale['invoice_no']}");
        return $sale;
    }
}
