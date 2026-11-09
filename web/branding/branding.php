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
              VALUES ('$user_id', 'Accessed Branding Page', '$ip_address', '$user_agent')";
mysqli_query($con, $log_query);

// Fetch current branding data
$query = mysqli_query($con, "SELECT * FROM branding LIMIT 1");
$data = mysqli_fetch_assoc($query);
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
                                    <div class="card-title">Brandig Form</div>
                                </div>
                                <form action="branding_submit.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <?php if(isset($_GET['status'])): ?>
                                            <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                                <?php echo $_GET['status'] == 'success' ? 'Branding updated successfully!' : 'Error processing request.'; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="company_name">Company Name</label>
                                                    <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $data['company_name'] ?? ''; ?>" placeholder="Enter Company Name" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="company_email">Company Email</label>
                                                    <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo $data['company_email'] ?? ''; ?>" placeholder="Enter Company Email" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="telephone">Telephone No</label>
                                                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $data['telephone'] ?? ''; ?>" placeholder="Enter Telephone Number" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter Company Address"><?php echo $data['address'] ?? ''; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label for="company_logo">Logo Upload</label>
                                                    <?php if(!empty($data['company_logo'])): ?>
                                                        <div class="mb-2"><img src="../assets/img/branding/<?php echo $data['company_logo']; ?>" height="50"></div>
                                                    <?php endif; ?>
                                                    <input type="file" class="form-control-file" id="company_logo" name="company_logo" accept="image/*" />
                                                    <small class="form-text text-muted">Recommended size: 200x50px</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="company_favicon">Favicon Upload</label>
                                                    <?php if(!empty($data['company_favicon'])): ?>
                                                        <div class="mb-2"><img src="../assets/img/branding/<?php echo $data['company_favicon']; ?>" width="32"></div>
                                                    <?php endif; ?>
                                                    <input type="file" class="form-control-file" id="company_favicon" name="company_favicon" accept="image/x-icon, image/png" />
                                                    <small class="form-text text-muted">Recommended size: 32x32px</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_branding" class="btn btn-success">Submit</button>
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

</body>

</html>