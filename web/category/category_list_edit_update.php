<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    // Sanitize input
    $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
    $parent_category_id = mysqli_real_escape_string($con, $_POST['parent_category_id']);
    $sub_category_name = mysqli_real_escape_string($con, $_POST['sub_category_name']);

    // Update database
    $query = "UPDATE tblsub_category SET 
              parent_category_id = '$parent_category_id', 
              sub_category_name = '$sub_category_name' 
              WHERE id = '$category_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Updated category ID: $category_id. New Name: $sub_category_name, Parent ID: $parent_category_id";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Category', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: category_list.php?status=success");
    } else {
        header("Location: category_list.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
} else {
    header("Location: category_list.php");
    exit();
}
?>