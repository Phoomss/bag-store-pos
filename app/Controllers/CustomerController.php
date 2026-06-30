<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SupplierCustomerService;
use App\Helpers\Session;
use Exception;

class CustomerController extends Controller {
    protected SupplierCustomerService $service;

    public function __construct() {
        $this->service = new SupplierCustomerService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าข้อมูลลูกค้า']);
            return;
        }

        $customers = $this->service->getCustomers();
        $this->view('customers/index', ['customers' => $customers]);
    }

    public function create(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์สร้างโปรไฟล์ลูกค้า'], 403);
            return;
        }

        $body = $request->getBody();
        $name = trim($body['name'] ?? '');
        $phone = trim($body['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Name and Phone number are required.'], 400);
            return;
        }

        try {
            $success = $this->service->createCustomer($body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Customer profile created successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to create customer'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function update(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์แก้ไขข้อมูลลูกค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');
        $phone = trim($body['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Name and Phone number are required.'], 400);
            return;
        }

        try {
            $success = $this->service->updateCustomer($id, $body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Customer profile updated successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update customer'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function delete(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลลูกค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');
        try {
            $success = $this->service->deleteCustomer($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Customer profile deleted successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete customer'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function search(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ค้นหาข้อมูลลูกค้า'], 403);
            return;
        }

        $query = $request->get('query', '');
        $customers = $this->service->getCustomers($query);
        $this->json($customers);
    }
}
