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

        try {
            $success = $this->settingsService->updateSettings($body);
            if ($success) {
                // Update environment configurations if needed, or simply return success
                $this->json(['success' => true, 'message' => 'System settings updated successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to save settings.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
