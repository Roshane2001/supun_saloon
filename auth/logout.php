<?php
session_start();
include '../include/dbconnection.php';

// Log logout activity before session data is cleared
if (isset($_SESSION['staff_id'])) {
    $user_id = $_SESSION['staff_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
    $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent) 
                  VALUES ('$user_id', 'Logout', '$ip_address', '$user_agent')";
    mysqli_query($con, $log_query);
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();