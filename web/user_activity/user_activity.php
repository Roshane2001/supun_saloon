<?php
session_start(); // Start the session
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch user activity logs with staff names
$activity_query = mysqli_query($con, "SELECT ua.*, s.staff_name FROM tbluser_activity ua LEFT JOIN tblstaff s ON ua.user_id = s.id ORDER BY ua.created_at DESC");
$activity_data = [];
while ($row = mysqli_fetch_assoc($activity_query)) {
    $activity_data[] = $row;
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
                                        <h4 class="card-title">User Activity</h4>
                                        
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
                                                    <th>User</th>
                                                    <th>Action</th>
                                                    <th>IP Address</th>
                                                    <th>Details</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                                <?php foreach ($activity_data as $log): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['staff_name'] ?? 'System/Unknown'); ?></td>
                                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['details'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars(date('Y-m-d h:i A', strtotime($log['created_at']))); ?></td>
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

    <script>
        $(document).ready(function() {
            // Initialize DataTable for the category list
            $('#add-row').DataTable({
                "pageLength": 10,
                "order": [[ 0, "desc" ]] // Show newest logs first
            });
        });
    </script>
</body>

</html>