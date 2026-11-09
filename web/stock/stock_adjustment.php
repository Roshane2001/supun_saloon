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
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the stock adjustment interface to perform inventory corrections.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Stock Adjustment Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch active products for the dropdown
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
                        <div class="card-header"><div class="card-title">Stock Adjustment</div></div>
                        <form id="stockAdjustmentForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Product</label>
                                            <select class="form-select" name="product_id" required>
                                                <option value="">Select Product</option>
                                                <?php while($p = mysqli_fetch_assoc($products_query)): ?>
                                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['product_code'] . ' - ' . $p['product_name']; ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Adjustment Type</label>
                                            <select class="form-select" name="adjustment_type" required>
                                                <option value="">Select Type</option>
                                                <option value="Increase">Increase</option>
                                                <option value="Decrease">Decrease</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" step="0.01" class="form-control" name="quantity" placeholder="0.00" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Reason</label>
                                            <input type="text" class="form-control" name="reason" placeholder="e.g., Damaged, Inventory Count, Return" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Notes (Optional)</label>
                                            <textarea class="form-control" name="notes" rows="2" placeholder="Additional details about the adjustment"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">Apply Adjustment</button>
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
    <script>
    $(document).ready(function() {
        $('#stockAdjustmentForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();

            // Basic client-side validation
            var quantity = parseFloat($('[name="quantity"]').val());
            if (isNaN(quantity) || quantity <= 0) {
                swal("Error!", "Quantity must be a positive number.", "error");
                return;
            }
            if ($('[name="product_id"]').val() === "") {
                swal("Error!", "Please select a product.", "error");
                return;
            }
            if ($('[name="adjustment_type"]').val() === "") {
                swal("Error!", "Please select an adjustment type.", "error");
                return;
            }
            if ($('[name="reason"]').val().trim() === "") {
                swal("Error!", "Please provide a reason for the adjustment.", "error");
                return;
            }

            $.ajax({
                url: 'save_stock_adjustment.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
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