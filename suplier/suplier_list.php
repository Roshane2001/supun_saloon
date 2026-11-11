<?php
session_start(); // Start the session

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " viewed the supplier directory.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Supplier List Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch supplier data
$supplier_query = mysqli_query($con, "SELECT id, suplier_name, contact_person, tel_no, email, address, is_active FROM tblsuplier ORDER BY suplier_name ASC");
$supplier_data = [];
while ($row = mysqli_fetch_assoc($supplier_query)) {
    $supplier_data[] = $row;
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
                                        <h4 class="card-title">Supplier List</h4>
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
                                                    <th>#</th>
                                                    <th>Company Name</th>
                                                    <th>Contact Person</th>
                                                    <th>Telephone No</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($supplier_data as $supplier): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($supplier['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['suplier_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['tel_no']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['address']); ?></td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                <?php echo $supplier['is_active'] ? 'checked' : ''; ?>
                                                                onchange="window.location.href='suplier_status_toggle.php?id=<?php echo $supplier['id']; ?>'">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button"
                                                                class="btn btn-link btn-primary btn-lg btn-edit-supplier"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editSupplierModal"
                                                                data-id="<?php echo $supplier['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($supplier['suplier_name']); ?>"
                                                                data-contact-person="<?php echo htmlspecialchars($supplier['contact_person']); ?>"
                                                                data-tel-no="<?php echo htmlspecialchars($supplier['tel_no']); ?>"
                                                                data-email="<?php echo htmlspecialchars($supplier['email']); ?>"
                                                                data-address="<?php echo htmlspecialchars($supplier['address']); ?>"
                                                                title="Edit Supplier">
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
            <!-- Include Supplier Edit Modal Component -->
            <?php include 'suplier_list_edit.php'; ?>
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
            $(document).on('click', '.btn-edit-supplier', function() {
                var btn = $(this);
                $('#edit_supplier_id').val(btn.data('id'));
                $('#edit_suplier_name').val(btn.data('name'));
                $('#edit_contact_person').val(btn.data('contact-person'));
                $('#edit_tel_no').val(btn.data('tel-no'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_address').val(btn.data('address'));
            });
        });
    </script>
</body>

</html>