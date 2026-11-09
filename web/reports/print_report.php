<?php 
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';
include '../include/script.php'; // Include script.php for datetimepicker

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
              VALUES ('$user_id', 'Accessed Print Report Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header -->
    <?php include '../include/header.php';?>
    <!-- Add datetimepicker CSS -->
    <link rel="stylesheet"
        href="<?php echo $base_path; ?>assets/js/plugin/datepicker/bootstrap-datetimepicker.min.css" />
    <!-- Add Font Awesome for calendar icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
                <!--Day Sales Report-->
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Day Sales Report</div>
                                </div>
                                <form action="print_sales_report.php" method="GET" target="_blank" id="dayReportForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="from_date_day">From Date :</label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" name="from_date"
                                                            id="from_date_day" placeholder="Select Start Date"
                                                            value="<?php echo date('Y-m-d'); ?>" required>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="to_date">To Date :</label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" name="to_date"
                                                            id="to_date_day" placeholder="Select End Date"
                                                            value="<?php echo date('Y-m-d'); ?>" required>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-success">Generate Report</button>
                                        <button type="reset" class="btn btn-danger">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Monthly Sales Report-->
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Monthly Sales Report</div>
                                </div>
                                <form action="print_monthly_sales_report.php" method="GET" target="_blank"
                                    id="monthReportForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="month_filter">Select Month :</label>
                                                    <div class="input-group">
                                                        <input type="month" class="form-control" name="date_filter"
                                                            id="month_filter" placeholder="Select Month"
                                                            value="<?php echo date('Y-m'); ?>" required>
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-success">Generate Report</button>
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
        // Initialize datepickers for Day Sales Report
        $('#from_date_day, #to_date_day').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        // Initialize datepicker for Monthly Sales Report (month/year only)
        $('#month_filter').datetimepicker({
            format: 'YYYY-MM',
            viewMode: 'months',
            minViewMode: 'months'
        });

    });
    </script>

</body>

</html>