<?php

namespace App\Core;

class Controller {
    protected function view(string $view, array $params = [], string $layout = 'main'): void {
        $response = new Response();
        $response->renderView($view, $params, $layout);
    }

    protected function json(mixed $data, int $statusCode = 200): void {
        $response = new Response();
        $response->json($data, $statusCode);
    }

    protected function redirect(string $url): void {
        $response = new Response();
        $response->redirect($url);
    }
}
