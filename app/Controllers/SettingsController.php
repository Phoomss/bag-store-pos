<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\SettingsService;
use App\Helpers\Session;
use Exception;

class SettingsController extends Controller {
    protected SettingsService $settingsService;

    public function __construct() {
        $this->settingsService = new SettingsService();
    }

    public function index(Request $request, Response $response): void {
        // Enforce Owner check
        if (!Session::checkRole(['Owner'])) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'Only the Owner can access system settings.']);
            return;
        }

        $settings = $this->settingsService->getSettings();
        $this->view('settings/index', ['settings' => $settings]);
    }

    public function update(Request $request, Response $response): void {
        if (!Session::checkRole(['Owner'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'Access Denied.'], 403);
            return;
        }

        $body = $request->getBody();

        // Handle PromptPay QR Image upload
        $file = $request->file('promptpay_qr');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            if (!in_array(strtolower($ext), $allowed)) {
                $this->json(['error' => 'Validation Error', 'message' => 'Only image files are allowed.'], 400);
                return;
            }

            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/settings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'promptpay_qr_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $body['promptpay_qr_path'] = '/uploads/settings/' . $filename;
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to move uploaded file.'], 500);
                return;
            }
        }

        try {
            $success = $this->settingsService->updateSettings($body);
            if ($success) {
                $this->json(['success' => true, 'message' => 'System settings updated successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to save settings.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
