<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Handle date filtering
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';
$where_clause = "WHERE s.sale_status = 'Completed'";
if ($date_filter) {
    $where_clause .= " AND DATE_FORMAT(s.sale_date, '%Y-%m') = '" . mysqli_real_escape_string($con, $date_filter) . "'";
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
                ORDER BY report_date DESC";

$sales_result = mysqli_query($con, $sales_query);
$sales_data = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../include/header.php';?>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include '../include/sidebar.php';?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <?php include '../include/logo_header.php';?>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <?php include '../include/topbar.php';?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-title">Monthly Sales Report</div>
                                        <form method="GET" action="" class="form-inline">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="date_filter" id="date_filter" placeholder="Select Month" value="<?php echo htmlspecialchars($date_filter); ?>">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-search pe-1">
                                                        <i class="fa fa-search search-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="sales-report-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Day Sales (LKR)</th>
                                                    <th>Net Profit (LKR)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_sales_sum = 0;
                                                $total_profit_sum = 0;
                                                foreach ($sales_data as $sale): 
                                                    $total_sales_sum += $sale['day_sales'];
                                                    $total_profit_sum += $sale['net_profit'];
                                                ?>
                                                <tr>
                                                    <td><b><?php echo date('Y-m-d', strtotime($sale['report_date'])); ?></b></td>
                                                    <td><b><?php echo number_format($sale['day_sales'], 2); ?></b>
                                                    </td>
                                                    <td class="<?php echo $sale['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <b><?php echo number_format($sale['net_profit'], 2); ?></b>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th><b><?php echo number_format($total_sales_sum, 2); ?></b></th>
                                                    <th class="<?php echo $total_profit_sum >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <b><?php echo number_format($total_profit_sum, 2); ?></b>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../include/footer.php';?>
        </div>
    </div>

    <!-- include/script.php -->
    <?php include "../include/script.php"; ?>

    <script>
    $(document).ready(function() {
        // Initialize DataTable for the sales report table
        $('#sales-report-table').DataTable({
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ] // Default sort by Date & Time descending
        });

        // Initialize the datepicker
        $('#date_filter').datetimepicker({
            format: 'YYYY-MM',
            viewMode: 'months'
        });
    });
    </script>
</body>

</html>