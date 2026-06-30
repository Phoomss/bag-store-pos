<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Helpers\Session;
use App\Helpers\Logger;

class AuthService {
    protected UserRepository $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function login(string $email, string $password): bool {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            Logger::logLogin($email, 'Failed');
            return false;
        }

        if ($user['status'] !== 'Active') {
            Logger::logLogin($email, 'Failed', $user['id']);
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            Logger::logLogin($email, 'Failed', $user['id']);
            return false;
        }

        // Login success
        Session::setUser($user);
        Logger::logLogin($email, 'Success', $user['id']);
        Logger::log('Login', 'User successfully logged into the system', $user['id']);

        return true;
    }

    public function logout(): void {
        $userId = Session::get('user_id');
        if ($userId) {
            Logger::log('Logout', 'User logged out of the system', $userId);
        }
        Session::destroy();
    }
}
