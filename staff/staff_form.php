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
$details = "Staff member " . $_SESSION['staff_name'] . " accessed the staff registration form.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Staff Add Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

// Fetch roles for the dropdown
$roles_query = mysqli_query($con, "SELECT * FROM tblrole");
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
                                    <div class="card-title">Staff Add</div>
                                </div>
                                <form action="staff_submit.php" method="POST">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                            role="alert">
                                            <?php
                                            if ($_GET['status'] == 'success') {
                                                echo 'Staff added successfully!';
                                            } else {
                                                echo isset($_GET['message']) && $_GET['message'] !== ''
                                                    ? htmlspecialchars($_GET['message'])
                                                    : 'Error processing request.';
                                            }
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="staff_name">Staff Name</label>
                                                    <input type="text" class="form-control" id="staff_name"
                                                        name="staff_name" placeholder="Enter Staff Name" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="nic_no">NIC No.</label>
                                                    <input type="text" class="form-control" id="nic_no" name="nic_no"
                                                        placeholder="Enter NIC Number" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="telephone_no">Telephone No</label>
                                                    <input type="tel" class="form-control" id="telephone_no"
                                                        name="telephone_no" placeholder="Enter Telephone Number (10 digits)"
                                                        inputmode="numeric" pattern="0[0-9]{9}" maxlength="10"
                                                        title="Enter a 10-digit number starting with 0, e.g. 0771234567"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <textarea class="form-control" id="address" name="address" rows="3"
                                                        placeholder="Enter Address"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="username">User Name</label>
                                                    <input type="text" class="form-control" id="username"
                                                        name="username" placeholder="Enter User Name" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Enter Password" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="role_id">Staff Role</label>
                                                    <select class="form-select" id="role_id" name="role_id" required>
                                                        <option value="">Select Role</option>
                                                        <?php while($role = mysqli_fetch_assoc($roles_query)): ?>
                                                        <option value="<?php echo $role['id']; ?>">
                                                            <?php echo ucwords($role['role']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_staff" class="btn btn-success">Submit</button>
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
    function showDuplicateFeedback($input, isDuplicate, label) {
        $input.removeClass('is-valid is-invalid');
        $input.next('.invalid-feedback.duplicate-feedback').remove();
        if (isDuplicate) {
            $input.addClass('is-invalid');
            $('<div class="invalid-feedback duplicate-feedback">A staff member with this ' + label + ' already exists.</div>').insertAfter($input);
        }
    }

    function checkStaffDuplicate(fieldId, fieldName, label) {
        var value = $('#' + fieldId).val();
        if (!value || !value.trim()) {
            showDuplicateFeedback($('#' + fieldId), false, label);
            return;
        }
        $.get('check_duplicate.php', { field: fieldName, value: value }, function (res) {
            showDuplicateFeedback($('#' + fieldId), !!(res && res.duplicate), label);
        });
    }

    $('#nic_no').on('blur', function () { checkStaffDuplicate('nic_no', 'nic_no', 'NIC number'); });
    $('#telephone_no').on('blur', function () { checkStaffDuplicate('telephone_no', 'telephone_no', 'telephone number'); });
    $('#username').on('blur', function () { checkStaffDuplicate('username', 'username', 'username'); });

    $('form').on('submit', function (e) {
        if ($('.is-invalid').length) {
            e.preventDefault();
            swal('Error', 'Please fix the duplicate fields before submitting.', 'error');
        }
    });
    </script>

</body>

</html>