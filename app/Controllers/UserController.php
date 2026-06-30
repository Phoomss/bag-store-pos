<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\UserService;
use App\Helpers\Session;
use Exception;

class UserController extends Controller {
    protected UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function index(Request $request, Response $response): void {
        // Enforce Owner or Admin only RBAC check
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'You do not have permission to manage users.']);
            return;
        }

        $users = $this->userService->getUsers();
        $roles = $this->userService->getRoles();
        
        $this->view('users/index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function create(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'Access Denied.'], 403);
            return;
        }

        $body = $request->getBody();

        if (empty($body['name']) || empty($body['email']) || empty($body['password']) || empty($body['role_id'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Name, Email, Password, and Role are required.'], 400);
            return;
        }

        try {
            $success = $this->userService->createUser($body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'User registered successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to register user.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function update(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'Access Denied.'], 403);
            return;
        }

        $id = (int)$request->get('id');
        $body = $request->getBody();

        if (empty($body['name']) || empty($body['email']) || empty($body['role_id'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Name, Email, and Role are required.'], 400);
            return;
        }

        try {
            $success = $this->userService->updateUser($id, $body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'User account updated successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to update user account.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function delete(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'Access Denied.'], 403);
            return;
        }

        $id = (int)$request->get('id');

        try {
            $success = $this->userService->deleteUser($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'User account deleted successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete user account.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Conflict', 'message' => $e->getMessage()], 409);
        }
    }

    public function logs(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'You do not have permission to view activity logs.']);
            return;
        }

        $loginHistory = $this->userService->getLoginHistory();
        $auditLogs = $this->userService->getActivityLogs();

        $this->view('users/logs', [
            'login_history' => $loginHistory,
            'audit_logs' => $auditLogs
        ]);
    }
}
