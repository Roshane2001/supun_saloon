<?php 
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the Stock List page.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Stock List Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

$query = "SELECT p.product_code, p.product_name, p.price as selling_price,
                 pc.parent_category_name, sc.sub_category_name, sup.suplier_name, sc.id as sub_id,
                 s.*
          FROM tblproduct p
          LEFT JOIN tblstock s ON p.id = s.product_id
          LEFT JOIN tblparent_category pc ON p.parent_category_id = pc.id
          LEFT JOIN tblsub_category sc ON p.sub_category_id = sc.id
          LEFT JOIN tblsuplier sup ON p.suplier_id = sup.id
          WHERE p.is_active = 1";
$result = mysqli_query($con, $query);
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
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title">Inventory Overview</h4>
                            <div>
                                <a href="opening_stock.php" class="btn btn-sm btn-primary">Add Opening Stock</a>
                                <a href="stock_adjustment.php" class="btn btn-sm btn-warning">Adjustment</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="stock-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Supplier</th>
                                            <th>Selling</th>
                                            <th>Purchase</th>
                                            <th>In Hand</th>
                                            <th>Status</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result)): 
                                            $badge_class = 'badge-success';
                                            if($row['stock_status'] == 'Low Stock') $badge_class = 'badge-warning';
                                            if($row['stock_status'] == 'Out of Stock') $badge_class = 'badge-danger';
                                        ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?php echo $row['product_code']; ?></small><br>
                                                <b><?php echo $row['product_name']; ?></b>
                                            </td>
                                            <td><small><?php echo $row['parent_category_name'] . ' / ' . $row['sub_category_name']; ?></small></td>
                                            <td><?php echo $row['suplier_name'] ?? 'N/A'; ?></td>
                                            <td><?php echo number_format($row['selling_price'], 2); ?></td>
                                            <td><?php echo number_format($row['purchase_price'] ?? 0, 2); ?></td>
                                            <td>
                                                <b><?php echo number_format($row['quantity_on_hand'] ?? 0); ?></b>
                                                <br><small class="text-muted">Min: <?php echo $row['reorder_level'] ?? 0; ?></small>
                                            </td>
                                            <td><span class="badge <?php echo $badge_class; ?>"><?php echo $row['stock_status'] ?? 'Not Set'; ?></span></td>
                                            <td><?php echo number_format($row['stock_value'] ?? 0, 2); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
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
    <script>$(document).ready(function() { $('#stock-table').DataTable(); });</script>
</body>
</html>