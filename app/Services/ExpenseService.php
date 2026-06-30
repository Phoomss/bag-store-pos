<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;
use App\Helpers\Logger;
use App\Helpers\Session;
use Exception;

class ExpenseService {
    protected ExpenseRepository $expenseRepo;

    public function __construct() {
        $this->expenseRepo = new ExpenseRepository();
    }

    public function getExpenses(array $filters = []): array {
        return $this->expenseRepo->all($filters);
    }

    public function createExpense(array $data): bool {
        $data['user_id'] = Session::get('user_id');
        $success = $this->expenseRepo->create($data);
        if ($success) {
            Logger::log('Expense Logging', "Registered expense: {$data['category']} of {$data['amount']} THB");
        }
        return $success;
    }

    public function deleteExpense(int $id): bool {
        $expense = $this->expenseRepo->find($id);
        if (!$expense) {
            throw new Exception("Expense record not found.");
        }

        $success = $this->expenseRepo->delete($id);
        if ($success) {
            Logger::log('Expense Deletion', "Deleted expense record ID: {$id} ({$expense['category']} of {$expense['amount']} THB)");
        }
        return $success;
    }

    public function getExpenseTotalsByCategory(string $startDate, string $endDate): array {
        return $this->expenseRepo->getCategoryTotals($startDate, $endDate);
    }
}
