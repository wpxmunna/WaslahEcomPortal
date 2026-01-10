<?php
/**
 * Admin Finance Report Controller
 */

class AdminFinanceReportController extends Controller
{
    private FinancialReport $reportModel;

    public function __construct()
    {
        parent::__construct();

        // Require admin access
        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        // Only full admin can access financial reports
        if (Session::get('admin_role') !== 'admin') {
            $this->redirect('admin', 'Access denied', 'error');
        }

        $this->reportModel = new FinancialReport();
    }

    /**
     * Financial reports dashboard
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $summary = $this->reportModel->getDashboardSummary($storeId);

        // Default date range - this month
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        // Revenue trend for last 6 months
        $trendStart = date('Y-m-01', strtotime('-5 months'));
        $revenueTrend = $this->reportModel->getRevenueTrend($storeId, $trendStart, $endDate);

        $this->view('admin/finance-reports/index', [
            'pageTitle' => 'Financial Reports - Admin',
            'summary' => $summary,
            'revenueTrend' => $revenueTrend
        ], 'admin');
    }

    /**
     * Profit & Loss statement
     */
    public function profitLoss(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get date range from query params or default to this month
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));

        $report = $this->reportModel->getProfitLoss($storeId, $startDate, $endDate);

        $this->view('admin/finance-reports/profit-loss', [
            'pageTitle' => 'Profit & Loss - Financial Reports',
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    /**
     * Cash Flow statement
     */
    public function cashFlow(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));

        $report = $this->reportModel->getCashFlow($storeId, $startDate, $endDate);

        $this->view('admin/finance-reports/cash-flow', [
            'pageTitle' => 'Cash Flow - Financial Reports',
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    /**
     * Expense analysis report
     */
    public function expenseReport(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));

        $report = $this->reportModel->getExpenseAnalysis($storeId, $startDate, $endDate);

        $this->view('admin/finance-reports/expenses', [
            'pageTitle' => 'Expense Analysis - Financial Reports',
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    /**
     * Export report to CSV
     */
    public function export(string $type): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-t'));

        $filename = $type . '_report_' . $startDate . '_to_' . $endDate . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'profit-loss':
                $report = $this->reportModel->getProfitLoss($storeId, $startDate, $endDate);
                fputcsv($output, ['Profit & Loss Report', $startDate . ' to ' . $endDate]);
                fputcsv($output, []);
                fputcsv($output, ['REVENUE']);
                fputcsv($output, ['Gross Revenue', $report['revenue']['gross']]);
                fputcsv($output, ['Discounts', '-' . $report['revenue']['discounts']]);
                fputcsv($output, ['Net Revenue', $report['revenue']['net']]);
                fputcsv($output, []);
                fputcsv($output, ['COST OF GOODS SOLD', $report['cogs']]);
                fputcsv($output, []);
                fputcsv($output, ['GROSS PROFIT', $report['gross_profit']]);
                fputcsv($output, ['Gross Margin', number_format($report['gross_margin'], 2) . '%']);
                fputcsv($output, []);
                fputcsv($output, ['OPERATING EXPENSES']);
                foreach ($report['expenses']['by_category'] as $cat) {
                    fputcsv($output, [$cat['category'], $cat['amount']]);
                }
                fputcsv($output, ['Total Expenses', $report['expenses']['total']]);
                fputcsv($output, []);
                fputcsv($output, ['OPERATING PROFIT', $report['operating_profit']]);
                fputcsv($output, ['Net Margin', number_format($report['net_margin'], 2) . '%']);
                break;

            case 'cash-flow':
                $report = $this->reportModel->getCashFlow($storeId, $startDate, $endDate);
                fputcsv($output, ['Cash Flow Report', $startDate . ' to ' . $endDate]);
                fputcsv($output, []);
                fputcsv($output, ['CASH IN']);
                fputcsv($output, ['Order Revenue', $report['cash_in']['orders']]);
                fputcsv($output, ['Total Cash In', $report['cash_in']['total']]);
                fputcsv($output, []);
                fputcsv($output, ['CASH OUT']);
                fputcsv($output, ['Expenses Paid', $report['cash_out']['expenses']]);
                fputcsv($output, ['Supplier Payments', $report['cash_out']['supplier_payments']]);
                fputcsv($output, ['Refunds', $report['cash_out']['refunds']]);
                fputcsv($output, ['Total Cash Out', $report['cash_out']['total']]);
                fputcsv($output, []);
                fputcsv($output, ['NET CASH FLOW', $report['net_cash_flow']]);
                break;

            case 'expenses':
                $report = $this->reportModel->getExpenseAnalysis($storeId, $startDate, $endDate);
                fputcsv($output, ['Expense Report', $startDate . ' to ' . $endDate]);
                fputcsv($output, []);
                fputcsv($output, ['Category', 'Count', 'Total']);
                foreach ($report['by_category'] as $cat) {
                    fputcsv($output, [$cat['category'], $cat['count'], $cat['total']]);
                }
                fputcsv($output, []);
                fputcsv($output, ['SUMMARY']);
                fputcsv($output, ['Total Expenses', $report['summary']['count'], $report['summary']['total']]);
                fputcsv($output, ['Average', '', $report['summary']['average']]);
                break;
        }

        fclose($output);
        exit;
    }
}
