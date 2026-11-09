<?php
session_start();
include '../include/dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_suplier'])) {
    // Retrieve and sanitize form data
    $suplier_name = mysqli_real_escape_string($con, $_POST['suplier_name']);
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $tel_no = mysqli_real_escape_string($con, $_POST['tel_no']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    // SQL query to insert data into tblsuplier
    $query = "INSERT INTO tblsuplier (suplier_name, contact_person, tel_no, email, address) 
              VALUES ('$suplier_name', '$contact_person', '$tel_no', '$email', '$address')";

    if (mysqli_query($con, $query)) {
        // Log detailed activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        
        // Construct a detailed string of the data added
        $details = "Added new supplier: $suplier_name | Contact: $contact_person | Tel: $tel_no | Email: $email | Address: $address";
        
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Supplier', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        // Redirect with success status
        header("Location: suplier_add.php?status=success");
        exit();
    } else {
        // Redirect with error status
        header("Location: suplier_add.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }
} else {
    // If accessed directly, redirect to the form
    header("Location: suplier_add.php");
    exit();
}
?>