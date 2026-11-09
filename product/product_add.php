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
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent) 
              VALUES ('$user_id', 'Accessed Product Add Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);

// Fetch active parent categories for the dropdown
$parents_query = mysqli_query($con, "SELECT * FROM tblparent_category ORDER BY parent_category_name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header -->
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
                                    <div class="card-title">Product Add</div>
                                </div>
                                <form action="product_submit.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                            role="alert">
                                            <?php echo $_GET['status'] == 'success' ? 'Product added successfully!' : 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error'); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="parent_category_id">Parent Category</label>
                                                    <select class="form-select" id="parent_category_id"
                                                        name="parent_category_id" required>
                                                        <option value="">Select Parent Category</option>
                                                        <?php while($parent = mysqli_fetch_assoc($parents_query)): ?>
                                                        <option value="<?php echo $parent['id']; ?>">
                                                            <?php echo htmlspecialchars($parent['parent_category_name']); ?>
                                                        </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sub_category_id">Sub Category</label>
                                                    <select class="form-select" id="sub_category_id"
                                                        name="sub_category_id" required>
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="product_name">Product Name</label>
                                                    <input type="text" class="form-control" id="product_name"
                                                        name="product_name" placeholder="Enter Product Name" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="product_code">Product Code</label>
                                                    <input type="text" class="form-control" id="product_code"
                                                        name="product_code" placeholder="Enter Product Code">
                                                </div>
                                                <div class="form-group">
                                                    <label for="price">Price</label>
                                                    <input type="number" step="0.01" class="form-control" id="price"
                                                        name="price" placeholder="0.00" required />
                                                </div>

                                                <div class="form-group">
                                                    <label for="exampleFormControlFile1">Product Image</label>
                                                    <input type="file" class="form-control-file"
                                                        id="exampleFormControlFile1" name="product_image" accept="image/*" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_product"
                                            class="btn btn-success">Submit</button>
                                        <button type="reset" class="btn btn-danger">Cancel</button>
                                    </div>
                                </form>
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
        $('#parent_category_id').change(function() {
            var parentId = $(this).val();
            if (parentId) {
                $.ajax({
                    type: 'POST',
                    url: 'get_sub_categories.php',
                    data: {
                        parent_id: parentId
                    },
                    success: function(html) {
                        $('#sub_category_id').html(html);
                    }
                });
            } else {
                $('#sub_category_id').html('<option value="">Select Sub Category</option>');
            }
        });
    });
    </script>

</body>

</html>