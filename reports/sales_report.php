<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch Sales data with staff name and product items summary
$sales_query = "SELECT s.*, st.staff_name, 
                (SELECT GROUP_CONCAT(CONCAT(si.product_name, ' (', si.quantity, ')') SEPARATOR ', ') 
                 FROM tblsale_items si WHERE si.sale_id = s.id) as items_summary
                FROM tblsale s 
                LEFT JOIN tblstaff st ON s.created_by = st.id 
                WHERE s.sale_status = 'Completed'
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
                                    <div class="card-title">Sales Report</div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="sales-report-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Date & Time</th>
                                                    <th>Items Sold</th>
                                                    <th>Discount</th>
                                                    <th>Bill Total</th>
                                                    <th>Received</th>
                                                    <th>Change</th>
                                                    <th>Due</th>
                                                    <th>Method</th>
                                                    <th>Staff</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sales_data as $sale): ?>
                                                <tr>
                                                    <td><b><?php echo htmlspecialchars($sale['invoice_no']); ?></b></td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($sale['sale_date'])); ?>
                                                    </td>
                                                    <td><small><?php echo htmlspecialchars($sale['items_summary']); ?></small>
                                                    </td>
                                                    <td><?php echo number_format($sale['discount_amount'], 2); ?></td>
                                                    <td><b><?php echo number_format($sale['grand_total'], 2); ?></b>
                                                    </td>
                                                    <td><?php echo number_format($sale['amount_received'], 2); ?></td>
                                                    <td><?php echo number_format($sale['change_amount'], 2); ?></td>
                                                    <td><?php echo number_format($sale['due_amount'], 2); ?></td>
                                                    <td><span
                                                            class="badge badge-info"><?php echo htmlspecialchars($sale['payment_method']); ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($sale['staff_name'] ?? 'N/A'); ?>
                                                    </td>
                                                    <td>
                                                        <a href="../cart/print_receipt.php?invoice_no=<?php echo urlencode($sale['invoice_no']); ?>"
                                                            target="_blank" class="btn btn-sm btn-secondary"
                                                            title="Print Preview">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                        <a href="../cart/print_receipt.php?invoice_no=<?php echo urlencode($sale['invoice_no']); ?>"
                                                            target="_blank" class="btn btn-sm btn-info"
                                                            title="View Receipt">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
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
                [1, "desc"]
            ] // Default sort by Date & Time descending
        });

        // Initialize the datepicker
        $('#datepicker').datetimepicker({
            format: 'YYYY-MM-DD' // You can customize the format as needed
        });
        // Handle modal show event to load receipt content
        $('#receiptViewModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var invoiceNo = button.data('invoice-no'); // Extract info from data-* attributes
            var modal = $(this);
            var receiptModalBody = modal.find('#receiptModalBody');

            // Store invoice number on modal for the internal print button
            modal.attr('data-current-invoice', invoiceNo);

            // Load receipt content via AJAX
            receiptModalBody.html('Loading receipt...'); // Show loading message
            $.ajax({
                url: '../cart/print_receipt.php',
                type: 'GET',
                data: {
                    invoice_no: invoiceNo
                },
                success: function(response) {
                    receiptModalBody.html(response);
                },
                error: function() {
                    receiptModalBody.html(
                        '<p class="text-danger">Failed to load receipt.</p>');
                }
            });
        });

    });
    </script>
</body>

</html>