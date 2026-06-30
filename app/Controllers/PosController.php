<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SalesService;
use App\Services\ProductService;
use App\Services\SupplierCustomerService;
use App\Services\SettingsService;
use App\Helpers\Session;
use Exception;

class PosController extends Controller {
    protected SalesService $salesService;
    protected ProductService $productService;
    protected SupplierCustomerService $customerService;
    protected SettingsService $settingsService;

    public function __construct() {
        $this->salesService = new SalesService();
        $this->productService = new ProductService();
        $this->customerService = new SupplierCustomerService();
        $this->settingsService = new SettingsService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าใช้งานหน้าจอขายสินค้า (POS)']);
            return;
        }

        $products = $this->productService->getProducts(['status' => 'Active']);
        $customers = $this->customerService->getCustomers();
        $settings = $this->settingsService->getSettings();

        // Render POS in a custom minimal layouts context without full sidebar if wanted, or with main sidebar
        // Let's use layout 'pos' which is a clean, wider layout optimized for cashier efficiency
        $this->view('pos/index', [
            'products' => $products,
            'customers' => $customers,
            'settings' => $settings
        ], 'pos');
    }

    public function checkout(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการขายสินค้า'], 403);
            return;
        }

        $body = $request->getBody();

        if (empty($body['items']) || !is_array($body['items']) || empty($body['payment_method']) || !isset($body['total_amount'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Items, Payment Method, and Total Amount are required.'], 400);
            return;
        }

        try {
            $saleId = $this->salesService->checkout($body);
            if ($saleId) {
                $this->json(['success' => true, 'message' => 'Transaction completed successfully', 'sale_id' => $saleId]);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to complete transaction'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function receipt(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงใบเสร็จรับเงิน']);
            return;
        }

        $id = (int)$request->get('id');
        $sale = $this->salesService->getSaleDetails($id);
        if (!$sale) {
            $response->setStatusCode(404);
            $this->view('errors/404', ['message' => 'Receipt record not found.']);
            return;
        }

        $settings = $this->settingsService->getSettings();
        // Render minimal layout print receipt page
        $this->view('pos/receipt', [
            'sale' => $sale,
            'settings' => $settings
        ], 'receipt');
    }

    public function holdSale(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์พักบิลรายการสินค้า'], 403);
            return;
        }

        $body = $request->getBody();
        $body['status'] = 'Held';

        if (empty($body['items']) || !is_array($body['items'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Items are required to hold a sale.'], 400);
            return;
        }

        try {
            $saleId = $this->salesService->checkout($body);
            if ($saleId) {
                $this->json(['success' => true, 'message' => 'Sale held successfully', 'sale_id' => $saleId]);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to hold sale'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function listHeldSales(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ดึงรายการพักบิล'], 403);
            return;
        }

        $held = $this->salesService->getHeldSales();
        $this->json($held);
    }

    public function resumeSale(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการต่อจากบิลที่พักไว้'], 403);
            return;
        }

        $body = $request->getBody();
        $heldId = (int)($body['held_id'] ?? 0);

        if ($heldId <= 0) {
            $this->json(['error' => 'Validation Error', 'message' => 'Invalid Held Sale ID.'], 400);
            return;
        }

        try {
            $saleData = $this->salesService->resumeSale($heldId);
            $this->json(['success' => true, 'sale' => $saleData]);
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
