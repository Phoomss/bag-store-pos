<?php

namespace App\Services;

use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Helpers\Logger;

class BrandCategoryService {
    protected BrandRepository $brandRepo;
    protected CategoryRepository $categoryRepo;

    public function __construct() {
        $this->brandRepo = new BrandRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    // Brands
    public function getBrands(): array {
        return $this->brandRepo->all();
    }

    public function getBrand(int $id): ?array {
        return $this->brandRepo->find($id);
    }

    public function createBrand(array $data): bool {
        $success = $this->brandRepo->create($data);
        if ($success) {
            Logger::log('Brand Creation', "Created brand: {$data['name']}");
        }
        return $success;
    }

    public function updateBrand(int $id, array $data): bool {
        $brand = $this->brandRepo->find($id);
        if (!$brand) return false;

        $success = $this->brandRepo->update($id, $data);
        if ($success) {
            Logger::log('Brand Update', "Updated brand: {$brand['name']} to {$data['name']}");
        }
        return $success;
    }

    public function deleteBrand(int $id): bool {
        $brand = $this->brandRepo->find($id);
        if (!$brand) return false;

        $success = $this->brandRepo->delete($id);
        if ($success) {
            Logger::log('Brand Deletion', "Deleted brand: {$brand['name']}");
        }
        return $success;
    }

    // Categories
    public function getCategories(): array {
        return $this->categoryRepo->all();
    }

    public function getCategory(int $id): ?array {
        return $this->categoryRepo->find($id);
    }

    public function createCategory(array $data): bool {
        $success = $this->categoryRepo->create($data);
        if ($success) {
            Logger::log('Category Creation', "Created category: {$data['name']}");
        }
        return $success;
    }

    public function updateCategory(int $id, array $data): bool {
        $category = $this->categoryRepo->find($id);
        if (!$category) return false;

        $success = $this->categoryRepo->update($id, $data);
        if ($success) {
            Logger::log('Category Update', "Updated category: {$category['name']} to {$data['name']}");
        }
        return $success;
    }

    public function deleteCategory(int $id): bool {
        $category = $this->categoryRepo->find($id);
        if (!$category) return false;

        $success = $this->categoryRepo->delete($id);
        if ($success) {
            Logger::log('Category Deletion', "Deleted category: {$category['name']}");
        }
        return $success;
    }
}
