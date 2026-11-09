<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);

    // Fetch supplier name and current status for detailed logging
    $check_res = mysqli_query($con, "SELECT suplier_name, is_active FROM tblsuplier WHERE id = '$id'");
    $supplier = mysqli_fetch_assoc($check_res);
    $new_status = $supplier['is_active'] ? 'Inactive' : 'Active';

    // Toggle the is_active status (1 to 0 or 0 to 1)
    $query = "UPDATE tblsuplier SET is_active = NOT is_active WHERE id = '$id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Status toggled for Supplier: " . $supplier['suplier_name'] . " (ID: $id) | New Status: $new_status";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Toggle Supplier Status', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: suplier_list.php?status=success");
    } else {
        header("Location: suplier_list.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
}
header("Location: suplier_list.php");
exit();
?>