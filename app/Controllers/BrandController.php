<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\BrandCategoryService;
use App\Helpers\Session;
use Exception;

class BrandController extends Controller {
    protected BrandCategoryService $service;

    public function __construct() {
        $this->service = new BrandCategoryService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าจอจัดการแบรนด์สินค้า']);
            return;
        }

        $brands = $this->service->getBrands();
        $this->view('brands/index', ['brands' => $brands]);
    }

    public function create(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์สร้างแบรนด์สินค้าใหม่'], 403);
            return;
        }

        $body = $request->getBody();
        $name = trim($body['name'] ?? '');

        if (empty($name)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Brand name is required'], 400);
            return;
        }

        try {
            $success = $this->service->createBrand([
                'name' => $name,
                'description' => $body['description'] ?? null
            ]);
            
            if ($success) {
                $this->json(['success' => true, 'message' => 'Brand created successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to create brand'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function update(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์แก้ไขข้อมูลแบรนด์สินค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $body = $request->getBody();
        $name = trim($body['name'] ?? '');

        if (empty($name)) {
            $this->json(['error' => 'Validation Error', 'message' => 'Brand name is required'], 400);
            return;
        }

        try {
            $success = $this->service->updateBrand($id, [
                'name' => $name,
                'description' => $body['description'] ?? null
            ]);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Brand updated successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update brand'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function delete(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลแบรนด์สินค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');

        try {
            $success = $this->service->deleteBrand($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Brand deleted successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete brand'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }
}
