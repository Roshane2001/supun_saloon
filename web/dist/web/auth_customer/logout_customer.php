<?php
session_start();
include '../include/dbconnection.php';

// Log logout activity before session data is cleared
if (isset($_SESSION['customer_id'])) {
    $user_id = $_SESSION['customer_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
    $log_query = "INSERT INTO tbluser_activity (customer_id, action, ip_address, user_agent) 
                  VALUES ('$customer_id', 'Logout', '$ip_address', '$user_agent')";
    mysqli_query($con, $log_query);
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login_customer.php");
exit();