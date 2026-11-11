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
              VALUES ('$user_id', 'Accessed Staff List Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);

// Fetch staff data along with their role names
$staff_query = mysqli_query($con, "SELECT ts.id, ts.staff_name, ts.nic_no, ts.address, ts.telephone_no, ts.role_id, tr.role, ts.is_active, ts.username FROM tblstaff ts JOIN tblrole tr ON ts.role_id = tr.id");
$staff_data = [];
while ($row = mysqli_fetch_assoc($staff_query)) {
    $staff_data[] = $row;
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
                                                    <th>Name</th>
                                                    <th>NIC No.</th>
                                                    <th>User Name</th>
                                                    <th>Address</th>
                                                    <th>Telephone</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($staff_data as $staff): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($staff['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['staff_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['nic_no']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['address']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['telephone_no']); ?></td>
                                                    <td><?php echo htmlspecialchars($staff['role']); ?></td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch"
                                                                <?php echo $staff['is_active'] ? 'checked' : ''; ?>
                                                                <?php echo ((int) $staff['id'] === (int) $_SESSION['staff_id']) ? 'disabled title="You cannot change the status of your own account"' : 'title="Activate / Deactivate Staff"'; ?>
                                                                onchange="window.location.href='staff_status_toggle.php?id=<?php echo $staff['id']; ?>'">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" 
                                                                class="btn btn-link btn-primary btn-lg btn-edit-staff"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editStaffModal"
                                                                data-id="<?php echo $staff['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($staff['staff_name']); ?>"
                                                                data-nic="<?php echo htmlspecialchars($staff['nic_no']); ?>"
                                                                data-username="<?php echo htmlspecialchars($staff['username']); ?>"
                                                                data-address="<?php echo htmlspecialchars($staff['address']); ?>"
                                                                data-telephone="<?php echo htmlspecialchars($staff['telephone_no']); ?>"
                                                                data-role="<?php echo $staff['role_id']; ?>"
                                                                title="Edit Staff">
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

            <!-- Include Modal Component -->
            <?php include 'staff_edit.php'; ?>
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
            function showEditDuplicateFeedback($input, isDuplicate, label) {
                $input.removeClass('is-valid is-invalid');
                $input.next('.invalid-feedback.duplicate-feedback').remove();
                if (isDuplicate) {
                    $input.addClass('is-invalid');
                    $('<div class="invalid-feedback duplicate-feedback">A staff member with this ' + label + ' already exists.</div>').insertAfter($input);
                }
            }

            function checkEditDuplicate(fieldId, fieldName, label) {
                var $input = $('#' + fieldId);
                var value = $input.val();
                var originalValue = $input.data('original');
                if (originalValue !== undefined && String(value).trim() === String(originalValue).trim()) {
                    showEditDuplicateFeedback($input, false, label);
                    return;
                }
                var excludeId = $('#edit_staff_id').val();
                if (!value || !value.trim()) {
                    showEditDuplicateFeedback($input, false, label);
                    return;
                }
                $.get('check_duplicate.php', { field: fieldName, value: value, exclude_id: excludeId }, function (res) {
                    showEditDuplicateFeedback($input, !!(res && res.duplicate), label);
                });
            }

            $('#edit_nic_no').on('blur', function () { checkEditDuplicate('edit_nic_no', 'nic_no', 'NIC number'); });
            $('#edit_telephone_no').on('blur', function () { checkEditDuplicate('edit_telephone_no', 'telephone_no', 'telephone number'); });
            $('#edit_username').on('blur', function () { checkEditDuplicate('edit_username', 'username', 'username'); });

            $('#editStaffModal form').on('submit', function (e) {
                if ($('#editStaffModal .is-invalid').length) {
                    e.preventDefault();
                    swal('Error', 'Please fix the duplicate fields before submitting.', 'error');
                }
            });

            // Populate Edit Modal on click
            var currentStaffId = <?php echo (int) $_SESSION['staff_id']; ?>;
            $(document).on('click', '.btn-edit-staff', function() {
                var btn = $(this);
                $('#editStaffModal .invalid-feedback.duplicate-feedback').remove();
                $('#editStaffModal .is-invalid').removeClass('is-invalid');
                $('#edit_staff_id').val(btn.data('id'));
                $('#edit_staff_name').val(btn.data('name'));
                $('#edit_nic_no').val(btn.data('nic')).data('original', btn.data('nic'));
                $('#edit_username').val(btn.data('username')).data('original', btn.data('username'));
                $('#edit_address').val(btn.data('address'));
                $('#edit_telephone_no').val(btn.data('telephone')).data('original', btn.data('telephone'));
                $('#edit_role_id').val(btn.data('role'));
                // A staff member cannot change their own role
                if (parseInt(btn.data('id')) === currentStaffId) {
                    $('#edit_role_id').prop('disabled', true);
                } else {
                    $('#edit_role_id').prop('disabled', false);
                }
            });
        });
    </script>
</body>

</html>