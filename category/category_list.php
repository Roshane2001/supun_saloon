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
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent) 
              VALUES ('$user_id', 'Accessed Category List Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);

// Fetch sub category data along with their parent category names
$category_query = mysqli_query($con, "SELECT ts.id, tp.parent_category_name, ts.sub_category_name, ts.parent_category_id, ts.is_active FROM tblsub_category ts JOIN tblparent_category tp ON ts.parent_category_id = tp.id");
$category_data = [];
while ($row = mysqli_fetch_assoc($category_query)) {
    $category_data[] = $row;
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
                                        <h4 class="card-title">Category List</h4>
                                        <!-- The "Add Row" button and modal are for a generic datatable example.
                                             Category addition is handled via category_form.php.-->
                                        <!--<button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                                            data-bs-target="#addRowModal">
                                            <i class="fa fa-plus"></i>
                                            Add Row
                                        </button>-->
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
                                                    <th>Parent Category</th>
                                                    <th>Sub Category Name</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($category_data as $cat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($cat['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($cat['parent_category_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($cat['sub_category_name']); ?></td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                <?php echo $cat['is_active'] ? 'checked' : ''; ?>
                                                                onchange="window.location.href='category_status_toggle.php?id=<?php echo $cat['id']; ?>'">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" 
                                                                class="btn btn-link btn-primary btn-lg btn-edit-category"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editCategoryModal"
                                                                data-id="<?php echo $cat['id']; ?>"
                                                                data-parent="<?php echo $cat['parent_category_id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($cat['sub_category_name']); ?>"
                                                                title="Edit Category">
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
            <!-- Include Modal Component -->
            <?php include 'category_list_edit.php'; ?>
        </div>       
    </div>

    <!-- include/script.php -->
    <?php include "../include/script.php"; ?>

    <script>
        $(document).ready(function() {
            // Initialize DataTable for the category list
            $('#add-row').DataTable({
                "pageLength": 10,
            });

            // Populate Edit Modal on click
            $(document).on('click', '.btn-edit-category', function() {
                var btn = $(this);
                $('#edit_category_id').val(btn.data('id'));
                $('#edit_parent_category_id').val(btn.data('parent'));
                $('#edit_sub_category_name').val(btn.data('name'));
            });
        });
    </script>
</body>

</html>