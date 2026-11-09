<?php 
session_start();
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
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the opening stock interface to initialize inventory levels.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Opening Stock Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch products that don't have stock yet (or all products for re-initialization)
$products_query = mysqli_query($con, "SELECT id, product_name, product_code FROM tblproduct WHERE is_active = 1 ORDER BY product_name ASC");
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
                            <div class="card-title">Set Opening Stock</div>
                        </div>
                        <form id="openingStockForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Product</label>
                                            <select class="form-select select2" name="product_id" id="product_id"
                                                required style="width: 100%">
                                                <option value="">-- Search Product Name or Code --</option>
                                                <?php while($p = mysqli_fetch_assoc($products_query)): ?>
                                                <option value="<?php echo $p['id']; ?>">
                                                    <?php echo $p['product_code'] . ' - ' . $p['product_name']; ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Opening Quantity</label>
                                            <input type="number" step="0.01" class="form-control" name="quantity"
                                                placeholder="0.00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Purchase Price (Unit Cost)</label>
                                            <input type="number" step="0.01" class="form-control" name="purchase_price"
                                                placeholder="0.00" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Reorder Level</label>
                                            <input type="number" step="0.01" class="form-control" name="reorder_level"
                                                placeholder="0.00" value="10">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Notes</label>
                                            <textarea class="form-control" name="notes"
                                                rows="2">Initial opening stock.</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">Save Stock</button>
                                <a href="stock_dashboard.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include '../include/footer.php';?>
        </div>
    </div>
    <?php include "../include/script.php"; ?>
    <!-- Select2 Plugin -->
    <script src="../assets/js/plugin/select2/select2.full.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2 for searchable product dropdown
        $('.select2').select2({
            theme: "bootstrap"
        });

        $('#openingStockForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'save_opening_stock.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        swal("Success!", response.message, "success").then(() => {
                            window.location.href = 'stock_list.php';
                        });
                    } else {
                        swal("Error!", response.message, "error");
                    }
                }
            });
        });
    });
    </script>
</body>

</html>