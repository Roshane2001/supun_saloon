<?php
session_start();
$base_path = '../'; // Define $base_path relative to the root
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_staff'])) {
    // Retrieve and sanitize form data
    $staff_name = mysqli_real_escape_string($con, $_POST['staff_name']);
    $nic_no = mysqli_real_escape_string($con, $_POST['nic_no']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    // Telephone number must contain exactly 10 digits (optional field)
    $telephone_no = trim($_POST['telephone_no'] ?? '');
    // Normalise the number so "077 123 4567" is accepted as 0771234567
    $telephone_no = preg_replace('/\D+/', '', $telephone_no);

    if ($telephone_no !== '' && !preg_match('/^0\d{9}$/', $telephone_no)) {
        header("Location: staff_form.php?status=error&message=" . urlencode("Please enter a valid 10-digit telephone number (e.g. 0771234567)."));
        exit();
    }

    $telephone_no = mysqli_real_escape_string($con, $telephone_no);
    $role_id = mysqli_real_escape_string($con, $_POST['role_id']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password']; // Get plain password

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // SQL query to insert data into tblstaff
    $query = "INSERT INTO tblstaff (staff_name, nic_no, address, telephone_no, role_id, username, password, is_active) 
              VALUES ('$staff_name', '$nic_no', '$address', '$telephone_no', '$role_id', '$username', '$hashed_password', 1)";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);

        // Fetch role name for detailed logging
        $role_res = mysqli_query($con, "SELECT role FROM tblrole WHERE id = '$role_id'");
        $role_name = mysqli_fetch_assoc($role_res)['role'] ?? 'Unknown';
        $details = "Added new staff: $staff_name (Username: $username, Role: " . ucwords($role_name) . ")";

        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Staff', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        // Redirect with success status
        header("Location: staff_form.php?status=success");
        exit();
    } else {
        // Redirect with error status
        header("Location: staff_form.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    // If accessed directly without POST data, redirect to the form
    header("Location: staff_form.php");
    exit();
}
?>