<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReportService;
use App\Helpers\Session;

class DashboardController extends Controller {
    protected ReportService $reportService;

    public function __construct() {
        $this->reportService = new ReportService();
    }

    public function index(Request $request, Response $response): void {
        $role = Session::get('user_role');
        if ($role === 'Cashier') {
            $stats = $this->reportService->getCashierDashboardStats((int)Session::get('user_id'));
            $this->view('dashboard/cashier', ['stats' => $stats]);
        } else if (!Session::hasPermission('manage_sales') && !Session::hasPermission('view_reports')) {
            $stats = $this->reportService->getWarehouseDashboardStats();
            $this->view('dashboard/warehouse', ['stats' => $stats]);
        } else {
            $stats = $this->reportService->getDashboardStats();
            $this->view('dashboard/index', ['stats' => $stats]);
        }
    }

    public function getStats(Request $request, Response $response): void {
        if (!Session::hasPermission('manage_sales') && !Session::hasPermission('view_reports')) {
            $this->json(['error' => 'Forbidden'], 403);
            return;
        }
        $period = $request->get('period', 'monthly');
        $chartData = $this->reportService->getRevenueAndProfitChartData($period);
        $this->json($chartData);
    }
}
