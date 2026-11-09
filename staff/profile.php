<?php
session_start();
$base_path = '../'; // Corrected path to point to the root from the 'staff' directory
include $base_path . 'include/dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Log page access for auditing with more detail
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed their profile page.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Profile Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);

//$staff_id = $_SESSION['staff_id']; // Needed for the AJAX call
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include $base_path . 'include/header.php'; ?>
    <title>My Profile</title>
</head>
<body>
    <div class="wrapper">
        <?php include $base_path . 'include/sidebar.php'; ?>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <?php include $base_path . 'include/logo_header.php'; ?>
                </div>
                <?php include $base_path . 'include/topbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">My Profile</div>
                                </div>
                                <form id="profileUpdateForm" action="update_profile.php" method="POST">
                                    <input type="hidden" name="staff_id" id="staff_id">
                                    <div class="card-body">
                                        <div id="profile-status-message" class="mb-3">
                                            <!-- AJAX messages will be displayed here -->
                                        </div>
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
                                                    <input type="text" class="form-control" id="telephone_no"
                                                        name="telephone_no" placeholder="Enter Telephone Number" />
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action"> 
                                        <button type="submit" name="update_staff" class="btn btn-success">Submit</button>
                                        <button type="reset" class="btn btn-danger">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include $base_path . 'include/footer.php'; ?>
        </div>
    </div>
    <?php include $base_path . 'include/script.php'; ?>
    <script>
        $(document).ready(function() {
            // Fetch user data via AJAX from fetch_profile_data.php
            $.ajax({
                url: 'fetch_profile_data.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        // Populate form inputs with retrieved data
                        $('#staff_id').val(data.id);
                        $('#staff_name').val(data.staff_name);
                        $('#nic_no').val(data.nic_no);
                        $('#telephone_no').val(data.telephone_no);
                        $('#address').val(data.address);
                        $('#username').val(data.username);
                    } else {
                        console.error("Error fetching data:", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                }
            });

            // Handle form submission via AJAX
            $('#profileUpdateForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize(); // Serialize form data
                const submitButton = form.find('button[type="submit"]');
                const originalButtonText = submitButton.text();

                submitButton.prop('disabled', true).text('Updating...'); // Disable button and show loading

                $.ajax({
                    url: form.attr('action'), // update_profile.php
                    type: form.attr('method'), // POST
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        let alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                        let messageHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                            ${response.message}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>`;
                        $('#profile-status-message').html(messageHtml);

                        if (response.status === 'success') {
                            // Optionally, re-fetch profile data to update displayed values if needed
                            // or update the topbar if staff_name was changed.
                            // window.location.reload(); // Uncomment if you want to reload the page on success
                        }
                    },
                    error: function(xhr, status, error) {
                        let messageHtml = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            An error occurred: ${error}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>`;
                        $('#profile-status-message').html(messageHtml);
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text(originalButtonText); // Re-enable button
                    }
                });
            });
        });
    </script>
</body>
</html>