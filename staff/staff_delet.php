<?php
session_start(); // Start the session
$base_path = '../'; // Define $base_path relative to the root
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page   
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $staff_id = mysqli_real_escape_string($con, $_GET['id']);

    // SQL query to delete data from tblstaff
    $query = "DELETE FROM tblstaff WHERE id = '$staff_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Deleted staff member ID: $staff_id";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Delete Staff', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        // Redirect back to list with success status
        header("Location: staff_list.php?status=success");
        exit();
    } else {
        // Redirect back to list with error status
        header("Location: staff_list.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    // If accessed without ID, redirect to the list
    header("Location: staff_list.php");
    exit();
}
?>