<?php

namespace App\Core;

class Response {
    public function setStatusCode(int $code): void {
        http_response_code($code);
    }

    public function json(mixed $data, int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        $this->setStatusCode($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    public function renderView(string $view, array $params = [], string $layout = 'main'): void {
        // Expose view variables
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        // Buffer the content view
        ob_start();
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";
        if (file_exists($viewFile)) {
            include_once $viewFile;
        } else {
            echo "View file [{$view}] not found.";
        }
        $viewContent = ob_get_clean();

        // Include the layout if layout is requested
        if ($layout) {
            $layoutFile = dirname(__DIR__, 2) . "/views/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                include_once $layoutFile;
            } else {
                echo $viewContent;
            }
        } else {
            echo $viewContent;
        }
    }
}
