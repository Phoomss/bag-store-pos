<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Session;

class CsrfMiddleware {
    public function handle(Request $request, Response $response): void {
        Session::start();

        if ($request->getMethod() === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!$token) {
                // If it's a JSON content body, parse it and try to check
                $body = $request->getBody();
                $token = $body['csrf_token'] ?? null;
            }

            if (!Session::validateCsrf($token)) {
                $response->setStatusCode(403);
                if ($request->isAjax()) {
                    $response->json(['error' => 'Forbidden', 'message' => 'CSRF verification failed. Request aborted.'], 403);
                } else {
                    $response->renderView('errors/403', ['message' => 'CSRF Verification Failed'], 'error');
                }
                exit;
            }
        }
    }
}
