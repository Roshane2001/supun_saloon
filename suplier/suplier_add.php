<?php 
session_start();
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
$details = "Staff member " . ($_SESSION['staff_name'] ?? 'Unknown') . " accessed the supplier registration page.";
$log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Supplier Add Page', '$ip_address', '$user_agent', '$details')";
mysqli_query($con, $log_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../include/header.php';?>
    <title>Add Supplier - POS System</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../include/sidebar.php';?>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo"><?php include '../include/logo_header.php';?></div>
                <?php include '../include/topbar.php';?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Add New Supplier</div>
                                </div>
                                <form action="suplier_submit.php" method="POST">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                        <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                            <?php 
                                                if($_GET['status'] == 'success') echo 'Supplier added successfully!';
                                                else echo 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error');
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="suplier_name">Supplier Name</label>
                                                    <input type="text" class="form-control" id="suplier_name" name="suplier_name" placeholder="Enter Supplier Name" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="contact_person">Contact Person</label>
                                                    <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Enter Contact Person Name" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="tel_no">Telephone No</label>
                                                    <input type="tel" class="form-control" id="tel_no" name="tel_no" placeholder="Enter Telephone Number (10 digits)"
                                                        inputmode="numeric" pattern="0[0-9]{9}" maxlength="10"
                                                        title="Enter a 10-digit number starting with 0, e.g. 0112345678"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email Address</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email Address" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <textarea class="form-control" id="address" name="address" rows="4" placeholder="Enter Supplier Address"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_suplier" class="btn btn-success">Submit</button>
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
    <?php include "../include/script.php"; ?>
</body>
</html>