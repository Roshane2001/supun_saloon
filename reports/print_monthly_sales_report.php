<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch Branding Info for report header
$branding_query = mysqli_query($con, "SELECT * FROM branding LIMIT 1");
$brand = mysqli_fetch_assoc($branding_query);

// Handle date filtering from GET parameters
$date_filter = isset($_GET['date_filter']) ? mysqli_real_escape_string($con, $_GET['date_filter']) : date('Y-m');
$where_clause = "WHERE s.sale_status = 'Completed'";

// Log activity with selected month
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Generated Monthly Sales Report. Selected Month: $date_filter";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Generate Monthly Report', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

if ($date_filter) {
    $where_clause .= " AND DATE_FORMAT(s.sale_date, '%Y-%m') = '$date_filter'";
}

// Aggregated query for daily sales and profit
$sales_query = "SELECT
                    DATE(s.sale_date) as report_date,
                    SUM(s.grand_total) as day_sales,
                    SUM(s.grand_total) - SUM(COALESCE(item_costs.total_cost, 0)) as net_profit
                FROM tblsale s
                LEFT JOIN (
                    SELECT sale_id, SUM(quantity * purchase_price_at_sale) as total_cost
                    FROM tblsale_items
                    GROUP BY sale_id
                ) item_costs ON s.id = item_costs.sale_id
                $where_clause
                GROUP BY DATE(s.sale_date)
                ORDER BY report_date ASC"; // Order by date ascending for chronological report

$sales_result = mysqli_query($con, $sales_query);
$sales_data = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $row;
}

// Calculate overall totals
$overall_total_sales = 0;
$overall_total_profit = 0;
foreach ($sales_data as $sale) {
    $overall_total_sales += $sale['day_sales'];
    $overall_total_profit += $sale['net_profit'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Sales Report - <?php echo $date_filter; ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <style>
        body { background: #fff; font-size: 11px; color: #000; margin: 0; padding: 0; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h2 { text-transform: uppercase; margin-bottom: 2px; font-weight: bold; }
        .table th, .table td { padding: 4px; border: 1px solid #444 !important; }
        .text-right { text-align: right; }
        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }
        .table tfoot th { border-top: 2px solid #000 !important; }
        @media print {
            .no-print { display: none; }
            @page { margin: 1cm; }
            body { padding: 0; }
            .container { width: 100% !important; max-width: 100% !important; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
<div class="container">
    <div class="no-print py-3 text-right">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Print Now</button>
        <button onclick="window.close()" class="btn btn-danger btn-sm">Close Window</button>
    </div>

    <div class="report-header">
        <h2><?php echo htmlspecialchars($brand['company_name'] ?? 'POS System'); ?></h2>
        <p>
            <?php echo htmlspecialchars($brand['address'] ?? ''); ?><br>
            Tel: <?php echo htmlspecialchars($brand['telephone'] ?? ''); ?> | Email: <?php echo htmlspecialchars($brand['company_email'] ?? ''); ?>
        </p>
        <hr>
        <h4 class="font-weight-bold">MONTHLY SALES SUMMARY REPORT</h4>
        <p>Month: <b><?php echo date('F Y', strtotime($date_filter . '-01')); ?></b></p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-right">Day Sales (LKR)</th>
                        <th class="text-right">Net Profit (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales_data)): ?>
                    <tr>
                        <td colspan="3" class="text-center">No sales data available for this month.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($sales_data as $sale): ?>
                        <tr>
                            <td><b><?php echo date('Y-m-d', strtotime($sale['report_date'])); ?></b></td>
                            <td class="text-right"><b><?php echo number_format($sale['day_sales'], 2); ?></b></td>
                            <td class="text-right <?php echo $sale['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <b><?php echo number_format($sale['net_profit'], 2); ?></b>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right"><b><?php echo number_format($overall_total_sales, 2); ?></b></th>
                        <th class="text-right <?php echo $overall_total_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <b><?php echo number_format($overall_total_profit, 2); ?></b>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    };
    window.onafterprint = function() {
        window.close();
    };
</script>
</body>
</html>