<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Helpers\Session;

class AuthController extends Controller {
    protected AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function loginView(Request $request, Response $response): void {
        Session::start();
        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        // Render login screen without the standard main layout
        $this->view('auth/login', [], 'auth');
    }

    public function login(Request $request, Response $response): void {
        $body = $request->getBody();
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            if ($request->isAjax()) {
                $response->json(['error' => 'Validation Error', 'message' => 'Email and password are required'], 400);
            } else {
                Session::flash('error', 'Email and password are required.');
                $this->redirect('/login');
            }
            return;
        }

        $success = $this->authService->login($email, $password);

        if ($success) {
            if ($request->isAjax()) {
                $response->json(['success' => true, 'redirect' => '/']);
            } else {
                $this->redirect('/');
            }
        } else {
            if ($request->isAjax()) {
                $response->json(['error' => 'Unauthorized', 'message' => 'Invalid email or password'], 401);
            } else {
                Session::flash('error', 'Invalid email or password.');
                $this->redirect('/login');
            }
        }
    }

    public function logout(Request $request, Response $response): void {
        $this->authService->logout();
        $this->redirect('/login');
    }
}
