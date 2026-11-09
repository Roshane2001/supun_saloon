<?php 
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch stock movement data with product and staff names
$query = "SELECT sm.created_at, sm.movement_type, sm.quantity_in, sm.quantity_out, sm.balance_after, sm.unit_cost, sm.reference_no, sm.notes,
                 p.product_name, p.product_code,
                 ts.staff_name
          FROM tblstock_movement sm
          JOIN tblproduct p ON sm.product_id = p.id
          LEFT JOIN tblstaff ts ON sm.created_by = ts.id
          ORDER BY sm.created_at DESC";
$result = mysqli_query($con, $query);

$movement_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $movement_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include '../include/header.php';?></head>
<body>
    <div class="wrapper">
        <?php include '../include/sidebar.php';?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo"><?php include '../include/logo_header.php';?></div>
                <?php include '../include/topbar.php';?>
            </div>
            <div class="container">
                <div class="page-inner">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Stock Movement History</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="stock-history-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Product</th>
                                            <th>Movement Type</th>
                                            <th>Qty In</th>
                                            <th>Qty Out</th>
                                            <th>Balance After</th>
                                            <th>Unit Cost</th>
                                            <th>Reference No</th>
                                            <th>Notes</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php foreach ($movement_data as $movement): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($movement['created_at']); ?></td>
                                            <td>
                                                <small class="text-muted"><?php echo htmlspecialchars($movement['product_code']); ?></small><br>
                                                <b><?php echo htmlspecialchars($movement['product_name']); ?></b>
                                            </td>
                                            <td><?php echo htmlspecialchars($movement['movement_type']); ?></td>
                                            <td><?php echo number_format($movement['quantity_in']); ?></td>
                                            <td><?php echo number_format($movement['quantity_out']); ?></td>
                                            <td><?php echo number_format($movement['balance_after']); ?></td>
                                            <td><?php echo number_format($movement['unit_cost'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($movement['reference_no'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($movement['notes'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($movement['staff_name'] ?? 'System'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../include/footer.php';?>
        </div>
    </div>
    <?php include "../include/script.php"; ?>
    <script>
    $(document).ready(function() {
        $('#stock-history-table').DataTable({
            "pageLength": 10,
            "order": [[0, "desc"]] // Order by date descending by default
        });
    });
    </script>
</body>
</html>