<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\PurchaseService;
use App\Services\SupplierCustomerService;
use App\Services\ProductService;
use App\Helpers\Session;
use Exception;

class PurchaseController extends Controller {
    protected PurchaseService $purchaseService;
    protected SupplierCustomerService $supplierService;
    protected ProductService $productService;

    public function __construct() {
        $this->purchaseService = new PurchaseService();
        $this->supplierService = new SupplierCustomerService();
        $this->productService = new ProductService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูประวัติใบสั่งซื้อสินค้า']);
            return;
        }

        $purchases = $this->purchaseService->getPurchases();
        $this->view('purchases/index', ['purchases' => $purchases]);
    }

    public function createView(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าใช้งานระบบสร้างใบสั่งซื้อใหม่']);
            return;
        }

        $suppliers = $this->supplierService->getSuppliers();
        $products = $this->productService->getProducts(['status' => 'Active']);
        $this->view('purchases/create', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function store(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการบันทึกใบสั่งซื้อ'], 403);
            return;
        }

        $body = $request->getBody();

        // Validate items and supplier
        if (empty($body['supplier_id']) || empty($body['order_date']) || empty($body['items']) || !is_array($body['items'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Supplier, Order Date, and at least one item are required.'], 400);
            return;
        }

        try {
            $purchaseId = $this->purchaseService->createPurchase($body);
            if ($purchaseId) {
                $this->json(['success' => true, 'message' => 'Purchase order recorded successfully', 'purchase_id' => $purchaseId]);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to create purchase order'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function viewPurchase(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูรายละเอียดใบสั่งซื้อสินค้า']);
            return;
        }

        $id = (int)$request->get('id');
        $purchase = $this->purchaseService->getPurchaseDetails($id);
        if (!$purchase) {
            $response->setStatusCode(404);
            $this->view('errors/404', ['message' => 'Purchase record not found.']);
            return;
        }

        $this->view('purchases/view', ['purchase' => $purchase]);
    }

    public function addPayment(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการบันทึกการชำระเงินค่าจัดซื้อ'], 403);
            return;
        }

        $purchaseId = (int)$request->get('id');
        $body = $request->getBody();

        if (empty($body['amount']) || empty($body['payment_method']) || empty($body['payment_date'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Amount, Payment Method, and Date are required.'], 400);
            return;
        }

        try {
            $paymentData = [
                'purchase_id' => $purchaseId,
                'amount' => (float)$body['amount'],
                'payment_method' => $body['payment_method'],
                'payment_date' => $body['payment_date'],
                'reference_no' => $body['reference_no'] ?? null
            ];

            $success = $this->purchaseService->addPayment($purchaseId, $paymentData);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Payment registered successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to add payment'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์อัปเดตสถานะใบสั่งซื้อสินค้า'], 403);
            return;
        }

        $purchaseId = (int)$request->get('id');
        $body = $request->getBody();
        $status = $body['status'] ?? '';
        $invoiceNo = $body['invoice_no'] ?? null;

        if (empty($status)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Status is required.'], 400);
            return;
        }

        try {
            $success = $this->purchaseService->updateStatus($purchaseId, $status, $invoiceNo);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Purchase order status updated successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update status'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
