<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}


// Fetch servicers data 
$servicers_query = mysqli_query($con, "SELECT * FROM servicers");
$servicers_data = [];
while ($row = mysqli_fetch_assoc($servicers_query)) {
    $servicers_data[] = $row;
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
                                        <h4 class="card-title">Staff List</h4>
                                        <!-- The "Add Row" button and modal are for a generic datatable example.
                                             Staff addition is handled via staff_form.php.
                                        <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                                            data-bs-target="#addRowModal">
                                            <i class="fa fa-plus"></i>
                                            Add Row
                                        </button>-->
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Status Alerts -->
                                    <?php if(isset($_GET['status'])): ?>
                                    <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                        role="alert">
                                        <?php echo $_GET['status'] == 'success' ? 'Operation successful!' : 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error'); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Service Time</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($servicers_data as $servicer): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($servicer['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($servicer['servicer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($servicer['service_time']); ?>
                                                        minutes</td>
                                                    <td><?php echo htmlspecialchars($servicer['price']); ?></td>
                                                    <td>
                                                        <?php if ($servicer['status'] == 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-edit-servicer"
                                                            data-bs-toggle="modal" data-bs-target="#editServicerModal"
                                                            data-id="<?php echo $servicer['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($servicer['servicer_name']); ?>"
                                                            data-time="<?php echo htmlspecialchars($servicer['service_time']); ?>"
                                                            data-price="<?php echo htmlspecialchars($servicer['price']); ?>"
                                                            data-status="<?php echo $servicer['status']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
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

            <!-- Include Modal Component -->
            <?php include 'servicers_edit.php'; ?>
            <?php include '../include/footer.php';?>
        </div>
    </div>

    <!-- include/script.php -->
    <?php include "../include/script.php"; ?>

    <script>
    $(document).ready(function() {
        // Initialize DataTable for the staff list
        $('#add-row').DataTable({
            "pageLength": 10,
        });

        // Populate Edit Modal on click
        $(document).on('click', '.btn-edit-servicer', function() {
            var btn = $(this);
            $('#edit_servicer_id').val(btn.data('id'));
            $('#edit_servicer_name').val(btn.data('name'));
            $('#edit_service_time').val(btn.data('time'));
            $('#edit_price').val(btn.data('price'));
            $('#edit_status').val(btn.data('status'));

        });
    });
    </script>
</body>

</html>