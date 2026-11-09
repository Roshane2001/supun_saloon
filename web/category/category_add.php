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
              VALUES ('$user_id', 'Accessed Category Add Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);

// Fetch parent categories for the dropdown
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-title">Category Add</div>
                                    <!--<button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addParentCategoryModal">
                                        <span class="btn-label">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        New Parent category
                                    </button>-->
                                </div>
                                <form action="category_submit.php" method="POST">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                            role="alert">
                                            <?php echo $_GET['status'] == 'success' ? 'Operation successful!' : 'Error processing request.'; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="largeSelect">Parent Category</label>
                                                    <select class="form-select form-control-lg" id="largeSelect" name="parent_category_id" required>
                                                        <option value="">Select Parent Category</option>
                                                        <?php while($parent = mysqli_fetch_assoc($parents_query)): ?>
                                                            <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['parent_category_name']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="category_name">Sub Category Name</label>
                                                    <input type="text" class="form-control" id="category_name"
                                                        name="category_name" placeholder="Enter Category Name"
                                                        required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_category"
                                            class="btn btn-success">Submit</button>
                                        <button type="reset" class="btn btn-danger">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Include Modal Component -->
            <?php include 'parent_Category_add.php'; ?>
            <?php include '../include/footer.php';?>
        </div>
    </div>

    <!-- include/script.php -->
    <?php include "../include/script.php"; ?>

</body>

</html>