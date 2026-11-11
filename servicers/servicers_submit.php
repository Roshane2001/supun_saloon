<?php
session_start();
$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    // Retrieve and sanitize form data
    $servicer_name = mysqli_real_escape_string($con, $_POST['servicer_name']);
    $servicer_time = mysqli_real_escape_string($con, $_POST['service_time']);
    $price = mysqli_real_escape_string($con, $_POST['price']);

    $dup_check = mysqli_query($con, "SELECT id FROM servicers WHERE servicer_name = '$servicer_name' LIMIT 1");
    if ($dup_check && mysqli_num_rows($dup_check) > 0) {
        header("Location: servicers_add.php?status=error&message=" . urlencode('A service with the same name already exists.'));
        exit();
    }

    // SQL query to insert data into servicers
    $query = "INSERT INTO servicers (servicer_name, service_time, price) 
              VALUES ('$servicer_name', '$servicer_time', '$price')";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);

        $details = "Added new service: $servicer_name (Time: $servicer_time, Price: $price)";

        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Service', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        // Redirect with success status
        header("Location: servicers_add.php?status=success");
        exit();
    } else {
        // Redirect with error status
        header("Location: servicers_add.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    // If accessed directly without POST data, redirect to the form
    header("Location: servicers_add.php");
    exit();
}
?>
