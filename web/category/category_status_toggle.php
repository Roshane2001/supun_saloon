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

    // Toggle the is_active status (1 to 0 or 0 to 1)
    $query = "UPDATE tblsub_category SET is_active = NOT is_active WHERE id = '$id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Toggled active status for category ID: $id";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Toggle Category Status', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: category_list.php?status=success");
    } else {
        header("Location: category_list.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
}
header("Location: category_list.php");
exit();
?>