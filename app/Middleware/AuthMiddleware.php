<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Session;

class AuthMiddleware {
    public function handle(Request $request, Response $response): void {
        Session::start();

        if (!Session::isLoggedIn()) {
            if ($request->isAjax()) {
                $response->json(['error' => 'Unauthorized', 'message' => 'Your session has expired. Please login again.'], 401);
            } else {
                Session::flash('error', 'Please login to access the system.');
                $response->redirect('/login');
            }
        }
    }
}
