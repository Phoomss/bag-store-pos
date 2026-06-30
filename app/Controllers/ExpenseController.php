<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ExpenseService;
use App\Helpers\Session;
use Exception;

class ExpenseController extends Controller {
    protected ExpenseService $expenseService;

    public function __construct() {
        $this->expenseService = new ExpenseService();
    }

    public function index(Request $request, Response $response): void {
        // Enforce Owner or Admin only check
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $response->setStatusCode(403);
            $this->view('errors/403', ['message' => 'คุณไม่มีสิทธิ์เข้าถึงหน้าจัดการค่าใช้จ่าย']);
            return;
        }

        $filters = [
            'category' => $request->get('category'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ];

        $expenses = $this->expenseService->getExpenses($filters);
        
        $this->view('expenses/index', [
            'expenses' => $expenses,
            'filters' => $filters
        ]);
    }

    public function create(Request $request, Response $response): void {
        // Enforce Owner or Admin only check
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ทำรายการบันทึกค่าใช้จ่าย'], 403);
            return;
        }

        $body = $request->getBody();

        if (empty($body['category']) || empty($body['amount']) || empty($body['expense_date'])) {
            $this->json(['error' => 'Validation Error', 'message' => 'Category, Amount, and Expense Date are required.'], 400);
            return;
        }

        try {
            $success = $this->expenseService->createExpense([
                'category' => $body['category'],
                'amount' => (float)$body['amount'],
                'description' => $body['description'] ?? null,
                'expense_date' => $body['expense_date']
            ]);

            if ($success) {
                $this->json(['success' => true, 'message' => 'Expense logged successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to record expense.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, Response $response): void {
        // Enforce Owner or Admin only check
        if (!Session::checkRole(['Owner', 'Admin'])) {
            $this->json(['error' => 'Forbidden', 'message' => 'คุณไม่มีสิทธิ์ลบรายการค่าใช้จ่าย'], 403);
            return;
        }

        $id = (int)$request->get('id');

        try {
            $success = $this->expenseService->deleteExpense($id);
            if ($success) {
                $this->json(['success' => true, 'message' => 'Expense record deleted successfully.']);
            } else {
                $this->json(['error' => 'Server Error', 'message' => 'Failed to delete expense record.'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
