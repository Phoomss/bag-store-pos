<?php

namespace App\Core;

class Request {
    private array $routeParams = [];

    public function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPath(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position === false) {
            return $uri;
        }
        return substr($uri, 0, $position);
    }

    public function getBody(): array {
        $body = [];
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'POST') {
            // Read JSON input if Content-Type is application/json
            $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        $body[$key] = $this->sanitize($value);
                    }
                }
            } else {
                foreach ($_POST as $key => $value) {
                    $body[$key] = $this->sanitize($value);
                }
            }
        }
        return $body;
    }

    private function sanitize(mixed $value): mixed {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->sanitize($v);
            }
            return $value;
        }
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public function get(string $key, mixed $default = null): mixed {
        $body = $this->getBody();
        return $body[$key] ?? $this->routeParams[$key] ?? $default;
    }

    public function setRouteParams(array $params): void {
        $this->routeParams = $params;
    }

    public function getRouteParams(): array {
        return $this->routeParams;
    }

    public function file(string $key): ?array {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            return $_FILES[$key];
        }
        return null;
    }

    public function isAjax(): bool {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }
}
