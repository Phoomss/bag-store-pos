<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReportService;
use App\Services\ExpenseService;
use App\Helpers\Session;

class ReportController extends Controller {
    protected ReportService $reportService;
    protected ExpenseService $expenseService;

    public function __construct() {
        $this->reportService = new ReportService();
        $this->expenseService = new ExpenseService();
    }

    public function index(Request $request, Response $response): void {
        if (!Session::hasPermission('view_reports')) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าดูหน้าจอรายงานการวิเคราะห์ธุรกิจ']);
            return;
        }

        $startDate = $request->get('start_date', date('Y-m-01')); // defaults to current month start
        $endDate = $request->get('end_date', date('Y-m-t'));      // defaults to current month end

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate);
        $cashFlowReport = $this->reportService->getCashFlowReport($startDate, $endDate);
        $expenseTotals = $this->expenseService->getExpenseTotalsByCategory($startDate, $endDate);

        $this->view('reports/index', [
            'sales' => $salesReport['data'],
            'sales_totals' => $salesReport['totals'],
            'cash_flow' => $cashFlowReport['data'],
            'cash_flow_totals' => $cashFlowReport['totals'],
            'expenses' => $expenseTotals,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}
