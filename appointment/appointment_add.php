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
$Servicer_query = mysqli_query($con, "SELECT * FROM servicers ORDER BY servicer_name ASC");
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
                                    <div class="card-title">Appointment Add</div>
                                </div>
                                <form action="appointment_submit.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                            role="alert">
                                            <?php echo $_GET['status'] == 'success' ? 'Appointment added successfully!' : 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error'); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="service_id">Service Type</label>
                                                    <select class="form-select" id="service_id" name="service_id" required>
                                                        <option value="">Select Service Type</option>
                                                        name="parent_category_id" required>
                                                        <?php while($service_id = mysqli_fetch_assoc($Servicer_query)): ?>
                                                        <option value="<?php echo $service_id['id']; ?>">
                                                            <?php echo htmlspecialchars($service_id['servicer_name']); ?>
                                                        </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="customer_name">Customer Name</label>
                                                    <input type="text" class="form-control" id="customer_name"
                                                        name="customer_name" placeholder="Enter Customer Name" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="telephone_number">Telephone Number</label>
                                                    <input type="tel" class="form-control" id="telephone_number"
                                                        name="telephone_number" placeholder="Enter Telephone Number (10 digits)"
                                                        inputmode="numeric" pattern="0[0-9]{9}" maxlength="10"
                                                        title="Enter a 10-digit number starting with 0, e.g. 0771234567"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <input type="date" class="form-control" id="date"
                                                        name="date" placeholder="Enter Date"
                                                        min="<?php echo date('Y-m-d'); ?>" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="time">Time</label>
                                                    <input type="time" class="form-control" id="time"
                                                        name="time" placeholder="Enter Time" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="note">Note</label>
                                                    <input type="text" class="form-control" id="note"
                                                        name="note" placeholder="Enter Note (optional)" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_appointment"
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

    <!--<script>
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
    </script>-->

</body>

</html>