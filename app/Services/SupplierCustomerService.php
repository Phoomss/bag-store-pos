<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Repositories\SupplierRepository;
use App\Helpers\Logger;
use Exception;

class SupplierCustomerService {
    protected CustomerRepository $customerRepo;
    protected SupplierRepository $supplierRepo;

    public function __construct() {
        $this->customerRepo = new CustomerRepository();
        $this->supplierRepo = new SupplierRepository();
    }

    // Customers
    public function getCustomers(string $search = ''): array {
        return $this->customerRepo->all($search);
    }

    public function getCustomer(int $id): ?array {
        return $this->customerRepo->find($id);
    }

    public function createCustomer(array $data): bool {
        // Validate unique phone
        if ($this->customerRepo->findByPhone($data['phone'])) {
            throw new Exception("Customer with phone number '{$data['phone']}' already exists.");
        }

        $success = $this->customerRepo->create($data);
        if ($success) {
            Logger::log('Customer Creation', "Created customer Profile: {$data['name']} (Phone: {$data['phone']})");
        }
        return $success;
    }

    public function updateCustomer(int $id, array $data): bool {
        $customer = $this->customerRepo->find($id);
        if (!$customer) {
            throw new Exception("Customer profile not found.");
        }

        $byPhone = $this->customerRepo->findByPhone($data['phone']);
        if ($byPhone && $byPhone['id'] !== $id) {
            throw new Exception("Phone number '{$data['phone']}' is already assigned to another customer.");
        }

        $success = $this->customerRepo->update($id, $data);
        if ($success) {
            Logger::log('Customer Update', "Updated customer profile: {$customer['name']} (ID: {$id})");
        }
        return $success;
    }

    public function deleteCustomer(int $id): bool {
        $customer = $this->customerRepo->find($id);
        if (!$customer) {
            throw new Exception("Customer profile not found.");
        }
        if ($id === 1) {
            throw new Exception("Cannot delete Walk-in Customer.");
        }

        $success = $this->customerRepo->delete($id);
        if ($success) {
            Logger::log('Customer Deletion', "Deleted customer: {$customer['name']} (ID: {$id})");
        }
        return $success;
    }

    // Suppliers
    public function getSuppliers(): array {
        return $this->supplierRepo->all();
    }

    public function getSupplier(int $id): ?array {
        return $this->supplierRepo->find($id);
    }

    public function createSupplier(array $data): bool {
        $success = $this->supplierRepo->create($data);
        if ($success) {
            Logger::log('Supplier Creation', "Created supplier: {$data['name']}");
        }
        return $success;
    }

    public function updateSupplier(int $id, array $data): bool {
        $supplier = $this->supplierRepo->find($id);
        if (!$supplier) {
            throw new Exception("Supplier not found.");
        }

        $success = $this->supplierRepo->update($id, $data);
        if ($success) {
            Logger::log('Supplier Update', "Updated supplier: {$supplier['name']} to {$data['name']}");
        }
        return $success;
    }

    public function deleteSupplier(int $id): bool {
        $supplier = $this->supplierRepo->find($id);
        if (!$supplier) {
            throw new Exception("Supplier not found.");
        }

        $success = $this->supplierRepo->delete($id);
        if ($success) {
            Logger::log('Supplier Deletion', "Deleted supplier: {$supplier['name']}");
        }
        return $success;
    }

    public function getSupplierHistory(int $supplierId): array {
        return [
            'purchases' => $this->supplierRepo->getPurchaseHistory($supplierId),
            'payments' => $this->supplierRepo->getPaymentHistory($supplierId)
        ];
    }
}
