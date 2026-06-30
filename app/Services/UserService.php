<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Helpers\Logger;
use App\Helpers\Session;
use Exception;

class UserService {
    protected UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function getUsers(): array {
        return $this->userRepo->all();
    }

    public function getUser(int $id): ?array {
        return $this->userRepo->find($id);
    }

    public function getRoles(): array {
        return $this->userRepo->getRoles();
    }

    public function createUser(array $data): bool {
        // Validate email uniqueness
        if ($this->userRepo->findByEmail($data['email'])) {
            throw new Exception("Email '{$data['email']}' is already registered.");
        }

        $success = $this->userRepo->create($data);
        if ($success) {
            Logger::log('User Registration', "Registered new user account: {$data['name']} ({$data['email']})");
        }
        return $success;
    }

    public function updateUser(int $id, array $data): bool {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        // Validate email uniqueness excluding current ID
        $byEmail = $this->userRepo->findByEmail($data['email']);
        if ($byEmail && $byEmail['id'] !== $id) {
            throw new Exception("Email '{$data['email']}' is already assigned to another user.");
        }

        $success = $this->userRepo->update($id, $data);
        if ($success) {
            Logger::log('User Update', "Updated user account: {$user['name']} (ID: {$id})");
        }
        return $success;
    }

    public function deleteUser(int $id): bool {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        if ($id === 1) {
            throw new Exception("Owner account cannot be deleted.");
        }

        $success = $this->userRepo->delete($id);
        if ($success) {
            Logger::log('User Deletion', "Deleted user account: {$user['name']} (ID: {$id})");
        }
        return $success;
    }

    public function getLoginHistory(int $limit = 50): array {
        return $this->userRepo->getLoginHistory($limit);
    }

    public function getActivityLogs(int $limit = 100): array {
        return $this->userRepo->getAuditLogs($limit);
    }
}
