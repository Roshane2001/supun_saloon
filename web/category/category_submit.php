<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    // Sanitize inputs
    $parent_category_id = mysqli_real_escape_string($con, $_POST['parent_category_id']);
    $sub_category_name = mysqli_real_escape_string($con, $_POST['category_name']);

    // Insert into tblsub_category
    $query = "INSERT INTO tblsub_category (parent_category_id, sub_category_name) 
              VALUES ('$parent_category_id', '$sub_category_name')";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Added sub category: $sub_category_name under parent ID: $parent_category_id";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Sub Category', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        // Redirect back with success
        header("Location: category_add.php?status=success");
        exit();
    } else {
        // Redirect back with error
        header("Location: category_add.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: category_add.php");
    exit();
}
?>