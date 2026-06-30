<?php

namespace App\Services;

use App\Repositories\SettingsRepository;
use App\Helpers\Logger;
use App\Helpers\Session;

class SettingsService {
    protected SettingsRepository $settingsRepo;

    public function __construct() {
        $this->settingsRepo = new SettingsRepository();
    }

    public function getSettings(): array {
        return $this->settingsRepo->all();
    }

    public function getSetting(string $key, mixed $default = null): mixed {
        return $this->settingsRepo->get($key, $default);
    }

    public function updateSettings(array $data): bool {
        $success = $this->settingsRepo->setMultiple($data);
        if ($success) {
            Logger::log('Settings Update', "Updated system configurations settings");
        }
        return $success;
    }
}
