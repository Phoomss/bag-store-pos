<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\InventoryService;
use App\Services\ProductService;
use App\Helpers\Session;
use Exception;

class InventoryController extends Controller {
    protected InventoryService $inventoryService;
    protected ProductService $productService;

    public function __construct() {
        $this->inventoryService = new InventoryService();
        $this->productService = new ProductService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_inventory')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าปรับปรุงยอดสต็อกสินค้า']);
            return;
        }

        $products = $this->productService->getProducts(['status' => 'Active']);
        $adjustments = $this->inventoryService->getAdjustments();
        $this->view('inventory/index', [
            'products' => $products,
            'adjustments' => $adjustments
        ]);
    }

    public function adjust(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_inventory')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการปรับยอดสต็อก'], 403);
            return;
        }

        $body = $request->getBody();

        if (empty($body['product_id']) || empty($body['quantity']) || empty($body['type']) || empty($body['reason'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Product, Quantity, Adjustment Type, and Reason are required.'], 400);
            return;
        }

        try {
            $data = [
                'product_id' => (int)$body['product_id'],
                'quantity' => (int)$body['quantity'],
                'type' => $body['type'],
                'reason' => trim($body['reason'])
            ];

            $success = $this->inventoryService->adjustStock($data);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Stock adjusted successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to adjust stock'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function movement(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_inventory')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์ดูประวัติความเคลื่อนไหวสต็อกสินค้า']);
            return;
        }

        $filters = [
            'product_id' => $request->get('product_id'),
            'type' => $request->get('type'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ];

        $movements = $this->inventoryService->getMovements($filters);
        $products = $this->productService->getProducts(['status' => 'Active']);

        $this->view('inventory/movements', [
            'movements' => $movements,
            'products' => $products,
            'filters' => $filters
        ]);
    }

    public function audit(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_inventory')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าตรวจสอบสต็อกและมูลค่าสินทรัพย์']);
            return;
        }

        // Physical inventory list audit showing SKU, cost vs retail valuation
        $products = $this->productService->getProducts();
        $this->view('inventory/audit', ['products' => $products]);
    }
}
