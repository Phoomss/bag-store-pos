<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class ReportService {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getDashboardStats(): array {
        // Today's Sales
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales WHERE DATE(created_at) = CURDATE() AND status = 'Completed'");
        $todaySales = (float)$stmt->fetchColumn();

        // Weekly Sales
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status = 'Completed'");
        $weeklySales = (float)$stmt->fetchColumn();

        // Monthly Sales
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status = 'Completed'");
        $monthlySales = (float)$stmt->fetchColumn();

        // Low Stock count
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock AND stock_quantity > 0 AND status = 'Active'");
        $lowStock = (int)$stmt->fetchColumn();

        // Out of Stock count
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 0 AND status = 'Active'");
        $outOfStock = (int)$stmt->fetchColumn();

        // Inventory summary value (At Cost & At Retail)
        $stmt = $this->db->query("SELECT 
                                    COUNT(*) as total_items, 
                                    SUM(stock_quantity) as total_qty,
                                    SUM(stock_quantity * cost_price) as total_cost,
                                    SUM(stock_quantity * selling_price) as total_retail
                                  FROM products WHERE status = 'Active'");
        $invSum = $stmt->fetch();

        // Top Selling Products (Last 30 Days)
        $sqlTopProd = "SELECT p.name, p.sku, SUM(si.quantity) as qty_sold, SUM(si.subtotal) as total_revenue
                       FROM sale_items si
                       JOIN sales s ON si.sale_id = s.id
                       JOIN products p ON si.product_id = p.id
                       WHERE s.status = 'Completed' AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                       GROUP BY si.product_id
                       ORDER BY qty_sold DESC
                       LIMIT 5";
        $topProducts = $this->db->query($sqlTopProd)->fetchAll();

        // Top Customers (Last 30 Days)
        $sqlTopCust = "SELECT c.name, c.customer_code, c.phone, SUM(s.total_amount) as total_spent, COUNT(s.id) as visits
                       FROM sales s
                       JOIN customers c ON s.customer_id = c.id
                       WHERE s.status = 'Completed' AND c.id != 1
                       GROUP BY s.customer_id
                       ORDER BY total_spent DESC
                       LIMIT 5";
        $topCustomers = $this->db->query($sqlTopCust)->fetchAll();

        // Recent Sales
        $sqlRecent = "SELECT s.*, c.name as customer_name, u.name as cashier_name
                      FROM sales s
                      LEFT JOIN customers c ON s.customer_id = c.id
                      JOIN users u ON s.user_id = u.id
                      ORDER BY s.id DESC
                      LIMIT 5";
        $recentSales = $this->db->query($sqlRecent)->fetchAll();

        return [
            'today_sales' => $todaySales,
            'weekly_sales' => $weeklySales,
            'monthly_sales' => $monthlySales,
            'low_stock_count' => $lowStock,
            'out_of_stock_count' => $outOfStock,
            'inventory_summary' => [
                'total_items' => (int)($invSum['total_items'] ?? 0),
                'total_quantity' => (int)($invSum['total_qty'] ?? 0),
                'total_cost_value' => (float)($invSum['total_cost'] ?? 0.00),
                'total_retail_value' => (float)($invSum['total_retail'] ?? 0.00)
            ],
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'recent_sales' => $recentSales
        ];
    }

    public function getRevenueAndProfitChartData(string $period = 'monthly'): array {
        // We will return data for the last 30 days or last 12 months.
        // Let's do daily revenue & profit for the last 30 days
        $sql = "SELECT 
                    DATE(s.created_at) as sales_date,
                    SUM(s.total_amount) as total_revenue,
                    SUM(s.total_amount - (
                        SELECT SUM(si.quantity * p.cost_price) 
                        FROM sale_items si 
                        JOIN products p ON si.product_id = p.id 
                        WHERE si.sale_id = s.id
                    )) as total_profit
                FROM sales s
                WHERE s.status = 'Completed' AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(s.created_at)
                ORDER BY DATE(s.created_at) ASC";
        
        $results = $this->db->query($sql)->fetchAll();
        
        $labels = [];
        $revenue = [];
        $profit = [];

        foreach ($results as $row) {
            $labels[] = date('d M', strtotime($row['sales_date']));
            $revenue[] = (float)$row['total_revenue'];
            $profit[] = (float)$row['total_profit'];
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'profit' => $profit
        ];
    }

    public function getSalesReport(string $startDate, string $endDate): array {
        $stmt = $this->db->prepare("SELECT s.*, c.name as customer_name, u.name as cashier_name
                                    FROM sales s
                                    LEFT JOIN customers c ON s.customer_id = c.id
                                    JOIN users u ON s.user_id = u.id
                                    WHERE DATE(s.created_at) >= ? AND DATE(s.created_at) <= ?
                                    ORDER BY s.id DESC");
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll();

        // Calculate summary metrics
        $totals = [
            'total_sales' => 0.00,
            'total_discount' => 0.00,
            'total_vat' => 0.00,
            'total_profit' => 0.00,
            'count' => count($sales)
        ];

        foreach ($sales as $key => $sale) {
            $totals['total_sales'] += (float)$sale['total_amount'];
            $totals['total_discount'] += (float)$sale['discount_amount'];
            $totals['total_vat'] += (float)$sale['vat_amount'];

            // Fetch items cost
            $stmtCost = $this->db->prepare("SELECT SUM(si.quantity * p.cost_price) 
                                            FROM sale_items si 
                                            JOIN products p ON si.product_id = p.id 
                                            WHERE si.sale_id = ?");
            $stmtCost->execute([$sale['id']]);
            $cost = (float)$stmtCost->fetchColumn();
            
            $profit = (float)$sale['total_amount'] - $cost;
            $sales[$key]['profit'] = $profit;
            $totals['total_profit'] += $profit;
        }

        return [
            'data' => $sales,
            'totals' => $totals
        ];
    }

    public function getCashFlowReport(string $startDate, string $endDate): array {
        // Sales Inflows
        $stmt = $this->db->prepare("SELECT DATE(created_at) as date_ref, SUM(total_amount) as inflow 
                                    FROM sales 
                                    WHERE status = 'Completed' AND DATE(created_at) >= ? AND DATE(created_at) <= ? 
                                    GROUP BY DATE(created_at)");
        $stmt->execute([$startDate, $endDate]);
        $inflowsRaw = $stmt->fetchAll();

        // Expenses Outflows
        $stmt = $this->db->prepare("SELECT expense_date as date_ref, SUM(amount) as outflow 
                                    FROM expenses 
                                    WHERE expense_date >= ? AND expense_date <= ? 
                                    GROUP BY expense_date");
        $stmt->execute([$startDate, $endDate]);
        $outflowsRaw = $stmt->fetchAll();

        // Purchase Payments Outflows
        $stmt = $this->db->prepare("SELECT payment_date as date_ref, SUM(amount) as outflow 
                                    FROM purchase_payments 
                                    WHERE payment_date >= ? AND payment_date <= ? 
                                    GROUP BY payment_date");
        $stmt->execute([$startDate, $endDate]);
        $purchRaw = $stmt->fetchAll();

        // Map together by date
        $flow = [];
        foreach ($inflowsRaw as $row) {
            $date = $row['date_ref'];
            $flow[$date]['inflow'] = (float)$row['inflow'];
        }
        foreach ($outflowsRaw as $row) {
            $date = $row['date_ref'];
            $flow[$date]['outflow'] = ($flow[$date]['outflow'] ?? 0.00) + (float)$row['outflow'];
        }
        foreach ($purchRaw as $row) {
            $date = $row['date_ref'];
            $flow[$date]['outflow'] = ($flow[$date]['outflow'] ?? 0.00) + (float)$row['outflow'];
        }

        ksort($flow);

        $reportData = [];
        $totals = ['inflow' => 0.00, 'outflow' => 0.00, 'net' => 0.00];

        foreach ($flow as $date => $values) {
            $in = $values['inflow'] ?? 0.00;
            $out = $values['outflow'] ?? 0.00;
            $net = $in - $out;

            $reportData[] = [
                'date' => $date,
                'inflow' => $in,
                'outflow' => $out,
                'net' => $net
            ];

            $totals['inflow'] += $in;
            $totals['outflow'] += $out;
            $totals['net'] += $net;
        }

        return [
            'data' => $reportData,
            'totals' => $totals
        ];
    }

    public function getWarehouseDashboardStats(): array {
        // Total active products count
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE status = 'Active'");
        $totalItems = (int)$stmt->fetchColumn();

        // Total active products quantity
        $stmt = $this->db->query("SELECT COALESCE(SUM(stock_quantity), 0) FROM products WHERE status = 'Active'");
        $totalQty = (int)$stmt->fetchColumn();

        // Low stock count
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock AND stock_quantity > 0 AND status = 'Active'");
        $lowStock = (int)$stmt->fetchColumn();

        // Out of stock count
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 0 AND status = 'Active'");
        $outOfStock = (int)$stmt->fetchColumn();

        // Pending purchases count
        $stmt = $this->db->query("SELECT COUNT(*) FROM purchases WHERE status IN ('Ordered', 'Partial')");
        $pendingPurchasesCount = (int)$stmt->fetchColumn();

        // Detailed low stock items (LIMIT 5)
        $stmt = $this->db->query("SELECT id, name, sku, stock_quantity, min_stock FROM products WHERE stock_quantity <= min_stock AND status = 'Active' ORDER BY stock_quantity ASC LIMIT 5");
        $lowStockProducts = $stmt->fetchAll();

        // Recent manual adjustments (LIMIT 5)
        $stmt = $this->db->query("SELECT ia.*, p.name as product_name, p.sku, u.name as user_name 
                                  FROM inventory_adjustments ia
                                  JOIN products p ON ia.product_id = p.id
                                  JOIN users u ON ia.user_id = u.id
                                  ORDER BY ia.id DESC LIMIT 5");
        $recentAdjustments = $stmt->fetchAll();

        // Recent purchase orders (LIMIT 5)
        $stmt = $this->db->query("SELECT p.*, s.name as supplier_name 
                                  FROM purchases p
                                  JOIN suppliers s ON p.supplier_id = s.id
                                  ORDER BY p.id DESC LIMIT 5");
        $recentPurchases = $stmt->fetchAll();

        return [
            'total_items' => $totalItems,
            'total_quantity' => $totalQty,
            'low_stock_count' => $lowStock,
            'out_of_stock_count' => $outOfStock,
            'pending_purchases_count' => $pendingPurchasesCount,
            'low_stock_products' => $lowStockProducts,
            'recent_adjustments' => $recentAdjustments,
            'recent_purchases' => $recentPurchases
        ];
    }
}
