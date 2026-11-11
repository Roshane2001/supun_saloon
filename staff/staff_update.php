<?php
session_start(); // Start the session
$base_path = '../'; // Define $base_path relative to the root
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_staff'])) {
    // Retrieve and sanitize form data for update
    $staff_id = mysqli_real_escape_string($con, $_POST['staff_id']);
    $staff_name = mysqli_real_escape_string($con, $_POST['staff_name']);
    $nic_no = mysqli_real_escape_string($con, $_POST['nic_no']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    // Telephone number must contain exactly 10 digits (optional field)
    $telephone_no = trim($_POST['telephone_no'] ?? '');
    // Normalise the number so "077 123 4567" is accepted as 0771234567
    $telephone_no = preg_replace('/\D+/', '', $telephone_no);

    if ($telephone_no !== '' && !preg_match('/^0\d{9}$/', $telephone_no)) {
        header("Location: staff_list.php?status=error&message=" . urlencode("Please enter a valid 10-digit telephone number (e.g. 0771234567)."));
        exit();
    }

    $telephone_no = mysqli_real_escape_string($con, $telephone_no);
    $role_id = mysqli_real_escape_string($con, $_POST['role_id']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // SQL query to update data in tblstaff
    $query = "UPDATE tblstaff SET
              staff_name = '$staff_name',
              nic_no = '$nic_no',
              address = '$address',
              telephone_no = '$telephone_no',
              role_id = '$role_id',
              username = '$username'";

    // Only update password if a new one is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id = '$staff_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Updated staff ID: $staff_id. Name: $staff_name, Username: $username";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Staff', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: staff_list.php?status=success");
        exit();
    } else {
        header("Location: staff_list.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    header("Location: staff_list.php?status=error&message=" . urlencode("Invalid request for staff update."));
    exit();
}
?>