<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ProductService;
use App\Services\BrandCategoryService;
use App\Helpers\Session;
use Exception;

class ProductController extends Controller {
    protected ProductService $productService;
    protected BrandCategoryService $brandCategoryService;

    public function __construct() {
        $this->productService = new ProductService();
        $this->brandCategoryService = new BrandCategoryService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูหน้าจอจัดการข้อมูลสินค้า']);
            return;
        }

        $filters = [
            'brand_id' => $request->get('brand_id'),
            'category_id' => $request->get('category_id'),
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'stock_status' => $request->get('stock_status')
        ];

        $products = $this->productService->getProducts($filters);
        $brands = $this->brandCategoryService->getBrands();
        $categories = $this->brandCategoryService->getCategories();

        $this->view('products/index', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    public function createView(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์สร้างสินค้าใหม่']);
            return;
        }

        $brands = $this->brandCategoryService->getBrands();
        $categories = $this->brandCategoryService->getCategories();
        $this->view('products/create', [
            'brands' => $brands,
            'categories' => $categories
        ]);
    }

    public function create(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ดำเนินการบันทึกข้อมูลสินค้า'], 403);
            return;
        }

        $body = $request->getBody();
        $images = $request->file('images');

        // Validation
        if (empty($body['sku']) || empty($body['barcode']) || empty($body['name']) || empty($body['selling_price'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'SKU, Barcode, Name, and Selling Price are required.'], 400);
            return;
        }

        try {
            $productId = $this->productService->createProduct($body, $images ?? []);
            if ($productId) {
                $this->json(['success' => true, 'message' => 'Product created successfully', 'product_id' => $productId]);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to create product'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function editView(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์แก้ไขข้อมูลสินค้า']);
            return;
        }

        $id = (int)$request->get('id');
        $product = $this->productService->getProduct($id);
        if (!$product) {
            $response->setStatusCode(404);
            $this->view('errors/404', ['message' => 'Product not found']);
            return;
        }

        $brands = $this->brandCategoryService->getBrands();
        $categories = $this->brandCategoryService->getCategories();
        $this->view('products/edit', [
            'product' => $product,
            'brands' => $brands,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ดำเนินการอัปเดตข้อมูลสินค้า'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $body = $request->getBody();
        $images = $request->file('images');

        if (empty($body['sku']) || empty($body['barcode']) || empty($body['name']) || empty($body['selling_price'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'SKU, Barcode, Name, and Selling Price are required.'], 400);
            return;
        }

        try {
            $success = $this->productService->updateProduct($id, $body, $images ?? []);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update product'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function delete(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_products')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลสินค้าออกจากระบบ'], 403);
            return;
        }

        $id = (int)$request->get('id');
        try {
            $success = $this->productService->deleteProduct($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete product'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function search(Request $request, Response $response): void {
        // Search must be allowed for either manage_products OR manage_sales (POS cashier searching products)
        if (!Session::hasPermission('manage_products') && !Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ดึงรายชื่อสินค้า'], 403);
            return;
        }

        $query = $request->get('query', '');
        $products = $this->productService->searchProducts($query);
        $this->json($products);
    }

    public function getByBarcode(Request $request, Response $response): void {
        // Barcode scan lookup must be allowed for either manage_products OR manage_sales (POS cashier scanning barcodes)
        if (!Session::hasPermission('manage_products') && !Session::hasPermission('manage_sales')) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์เข้าถึงฟังก์ชันบาร์โค้ด'], 403);
            return;
        }

        $barcode = $request->get('barcode', '');
        $product = $this->productService->getProductByBarcode($barcode);
        if ($product) {
            $this->json($product);
        } else {
            $this->json(['error' => 'Not Found', 'message' => 'Product not found'], 404);
        }
    }
}
