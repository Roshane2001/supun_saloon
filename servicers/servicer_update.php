<?php
session_start(); // Start the session
$base_path = '../'; // Define $base_path relative to the root
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_servicer'])) {
    // Retrieve and sanitize form data for update
    $servicer_id = mysqli_real_escape_string($con, $_POST['servicer_id']);
    $servicer_name = mysqli_real_escape_string($con, $_POST['servicer_name']);
    $nic_no = mysqli_real_escape_string($con, $_POST['nic_no']);
    $service_time = mysqli_real_escape_string($con, $_POST['service_time']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    // SQL query to update data in tblservicers
    $query = "UPDATE servicers SET
              servicer_name = '$servicer_name',
              price = '$nic_no',
              service_time = '$service_time',
              status = '$status'";

    // Only update password if a new one is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id = '$servicer_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Updated servicer ID: $servicer_id. Name: $servicer_name, Username: $username";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Servicer', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: servicers_list.php?status=success");
        exit();
    } else {
        header("Location: servicers_list.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    header("Location: servicers_list.php?status=error&message=" . urlencode("Invalid request for servicer update."));
    exit();
}
?>