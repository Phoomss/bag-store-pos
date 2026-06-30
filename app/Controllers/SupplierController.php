<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SupplierCustomerService;
use App\Helpers\Session;
use Exception;

class SupplierController extends Controller {
    protected SupplierCustomerService $service;

    public function __construct() {
        $this->service = new SupplierCustomerService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าจอจัดการผู้จัดจำหน่าย']);
            return;
        }

        $suppliers = $this->service->getSuppliers();
        $this->view('suppliers/index', ['suppliers' => $suppliers]);
    }

    public function create(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์สร้างรายชื่อผู้จัดจำหน่ายใหม่'], 403);
            return;
        }

        $body = $request->getBody();
        $name = trim($body['name'] ?? '');

        if (empty($name)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Supplier Name is required.'], 400);
            return;
        }

        try {
            $success = $this->service->createSupplier($body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Supplier created successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to create supplier'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function update(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์แก้ไขข้อมูลผู้จัดจำหน่าย'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');

        if (empty($name)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Supplier Name is required.'], 400);
            return;
        }

        try {
            $success = $this->service->updateSupplier($id, $body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Supplier updated successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update supplier'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function delete(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ลบผู้จัดจำหน่ายออกจากระบบ'], 403);
            return;
        }

        $id = (int)$request->get('id');
        try {
            $success = $this->service->deleteSupplier($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Supplier deleted successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete supplier'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function history(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_purchases')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์เข้าดูประวัติผู้จัดจำหน่าย'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $supplier = $this->service->getSupplier($id);
        if (!$supplier) {
            $this->json(['error' => 'Not Found', 'message' => 'Supplier not found'], 404);
            return;
        }

        $history = $this->service->getSupplierHistory($id);
        $this->json([
            'supplier' => $supplier,
            'purchases' => $history['purchases'],
            'payments' => $history['payments']
        ]);
    }
}
