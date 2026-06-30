<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SalesService;
use App\Helpers\Session;
use Exception;

class SalesController extends Controller {
    protected SalesService $salesService;

    public function __construct() {
        $this->salesService = new SalesService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูประวัติใบเสร็จการขาย']);
            return;
        }

        $filters = [
            'status' => $request->get('status'),
            'payment_status' => $request->get('payment_status'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'search' => $request->get('search')
        ];

        $sales = $this->salesService->getSales($filters);
        $this->view('sales/index', [
            'sales' => $sales,
            'filters' => $filters
        ]);
    }

    public function viewInvoice(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูรายละเอียดใบเสร็จรับเงิน']);
            return;
        }

        $id = (int)$request->get('id');
        $sale = $this->salesService->getSaleDetails($id);
        if (!$sale) {
            $response->setStatusCode(404);
            $this->view('errors/404', ['message' => 'Sale record not found.']);
            return;
        }

        $this->view('sales/view', ['sale' => $sale]);
    }

    public function refund(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการคืนเงินสินค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');

        try {
            $success = $this->salesService->refund($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Invoice refunded and stocks reverted successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to process refund.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
