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
$from_date = isset($_GET['from_date']) ? mysqli_real_escape_string($con, $_GET['from_date']) : date('Y-m-d');
$to_date = isset($_GET['to_date']) ? mysqli_real_escape_string($con, $_GET['to_date']) : date('Y-m-d');

// Log activity with selected dates
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Generated Daily Sales Report. Selected Range: $from_date to $to_date";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Generate Daily Report', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch Sales data joined with staff and item summaries within the range
$sales_query = "SELECT s.*, st.staff_name, 
                (SELECT GROUP_CONCAT(CONCAT(si.product_name, ' (', si.quantity, ')') SEPARATOR ', ') 
                 FROM tblsale_items si WHERE si.sale_id = s.id) as items_summary
                FROM tblsale s 
                LEFT JOIN tblstaff st ON s.created_by = st.id 
                WHERE s.sale_status = 'Completed' 
                AND DATE(s.sale_date) BETWEEN '$from_date' AND '$to_date'
                ORDER BY s.sale_date DESC";
$sales_result = mysqli_query($con, $sales_query);
$sales_data = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - <?php echo $from_date; ?> to <?php echo $to_date; ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <style>
        body { background: #fff; font-size: 11px; color: #000; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h2 { text-transform: uppercase; margin-bottom: 2px; font-weight: bold; }
        .table th, .table td { padding: 4px; border: 1px solid #444 !important; }
        .text-right { text-align: right; }
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
        <h4 class="font-weight-bold">SALES SUMMARY REPORT</h4>
        <p>Period: <b><?php echo $from_date; ?></b> to <b><?php echo $to_date; ?></b></p>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Date & Time</th>
                        <th>Items Sold</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Bill Total</th>
                        <th class="text-right">Received</th>
                        <th class="text-right">Change</th>
                        <th class="text-right">Due</th>
                        <th>Method</th>
                        <th>Staff</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_sales_sum = 0;
                    foreach ($sales_data as $sale): 
                        $total_sales_sum += $sale['grand_total'];
                    ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($sale['invoice_no']); ?></b></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($sale['sale_date'])); ?></td>
                        <td><small><?php echo htmlspecialchars($sale['items_summary']); ?></small></td>
                        <td class="text-right"><?php echo number_format($sale['discount_amount'], 2); ?></td>
                        <td class="text-right"><b><?php echo number_format($sale['grand_total'], 2); ?></b></td>
                        <td class="text-right"><?php echo number_format($sale['amount_received'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($sale['change_amount'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($sale['due_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($sale['staff_name'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #eee;">
                        <th colspan="4" class="text-right">Grand Total:</th>
                        <th class="text-right">LKR <?php echo number_format($total_sales_sum, 2); ?></th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>