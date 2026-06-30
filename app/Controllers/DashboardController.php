<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReportService;

class DashboardController extends Controller {
    protected ReportService $reportService;

    public function __construct() {
        $this->reportService = new ReportService();
    }

    public function index(Request $request, Response $response): void {
        $stats = $this->reportService->getDashboardStats();
        $this->view('dashboard/index', ['stats' => $stats]);
    }

    public function getStats(Request $request, Response $response): void {
        $period = $request->get('period', 'monthly');
        $chartData = $this->reportService->getRevenueAndProfitChartData($period);
        $this->json($chartData);
    }
}
