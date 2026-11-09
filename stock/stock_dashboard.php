<?php 
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Dashboard Queries
$total_products = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tblproduct"))['count'];
$stock_stats = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity_on_hand) as total_qty, SUM(stock_value) as total_val FROM tblstock"));
$low_stock = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tblstock WHERE stock_status = 'Low Stock'"))['count'];
$out_of_stock = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM tblstock WHERE stock_status = 'Out of Stock'"))['count'];
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
                    <div class="row">
                        <!-- Total Products -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small"><i class="fas fa-box"></i></div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Products</p>
                                                <h4 class="card-title"><?php echo $total_products; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Quantity -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small"><i class="fas fa-layer-group"></i></div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Stock</p>
                                                <h4 class="card-title"><?php echo number_format($stock_stats['total_qty'] ?? 0); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Value -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-dollar-sign"></i></div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Stock Value</p>
                                                <h4 class="card-title"><?php echo number_format($stock_stats['total_val'] ?? 0, 2); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Out of Stock -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-danger bubble-shadow-small"><i class="fas fa-exclamation-triangle"></i></div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Stock Alerts</p>
                                                <h4 class="card-title"><?php echo $out_of_stock; ?> Empty</h4>
                                                <p class="text-warning small"><?php echo $low_stock; ?> Low Stock</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="opening_stock.php" class="btn btn-primary me-2"><i class="fa fa-plus"></i> Set Opening Stock</a>
                            <a href="stock_adjustment.php" class="btn btn-warning"><i class="fa fa-sync"></i> Stock Adjustment</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../include/footer.php';?>
        </div>
    </div>
    <?php include "../include/script.php"; ?>
</body>
</html>