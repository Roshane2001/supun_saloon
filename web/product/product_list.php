<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';


// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the Product List directory.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Product List Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch product data with joined category names
$product_query = mysqli_query($con, "SELECT p.*, pc.parent_category_name, sc.sub_category_name, s.suplier_name 
                                    FROM tblproduct p 
                                    LEFT JOIN tblparent_category pc ON p.parent_category_id = pc.id 
                                    LEFT JOIN tblsub_category sc ON p.sub_category_id = sc.id
                                    LEFT JOIN tblsuplier s ON p.suplier_id = s.id");
$product_data = [];
while ($row = mysqli_fetch_assoc($product_query)) {
    $product_data[] = $row;
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Product List</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Status Alerts -->
                                    <?php if(isset($_GET['status'])): ?>
                                    <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $_GET['status'] == 'success' ? 'Operation successful!' : 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error'); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Image</th>
                                                    <th>Parent Category</th>
                                                    <th>Sub Category</th>
                                                    <th>Supplier</th>
                                                    <th>Product Name</th>
                                                    <th>Code</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($product_data as $product): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                                    <td>
                                                        <?php if(!empty($product['image'])): ?>
                                                            <img src="../assets/img/products/<?php echo $product['image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                        <?php else: ?>
                                                            <i class="fas fa-image text-muted" style="font-size: 24px;"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['parent_category_name'] ?? 'Uncategorized'); ?></td>
                                                    <td><?php echo htmlspecialchars($product['sub_category_name'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($product['suplier_name'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                                                    <td><?php echo number_format($product['price'], 2); ?></td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                <?php echo $product['is_active'] ? 'checked' : ''; ?>
                                                                onchange="window.location.href='product_status_toggle.php?id=<?php echo $product['id']; ?>'">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" 
                                                                class="btn btn-link btn-primary btn-lg btn-edit-product"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editProductModal"
                                                                data-id="<?php echo $product['id']; ?>"
                                                                data-parent="<?php echo $product['parent_category_id']; ?>"
                                                                data-sub="<?php echo $product['sub_category_id']; ?>"
                                                                data-suplier="<?php echo $product['suplier_id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                                data-code="<?php echo htmlspecialchars($product['product_code']); ?>"
                                                                data-price="<?php echo $product['price']; ?>"
                                                                title="Edit Product">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                        </div>
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

    <!-- Include Modal Component -->
    <?php include 'product_list_edit.php'; ?>

    <script>
        $(document).ready(function() {
            // Initialize DataTable for the product list
            $('#add-row').DataTable({
                "pageLength": 10,
            });
        });
    </script>
</body>

</html>