<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_suplier'])) {
    // Sanitize inputs
    $supplier_id = mysqli_real_escape_string($con, $_POST['supplier_id']);
    $suplier_name = mysqli_real_escape_string($con, $_POST['suplier_name']);
    $contact_person = mysqli_real_escape_string($con, $_POST['contact_person']);
    $tel_no = mysqli_real_escape_string($con, $_POST['tel_no']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    // Update query
    $query = "UPDATE tblsuplier SET 
              suplier_name = '$suplier_name', 
              contact_person = '$contact_person', 
              tel_no = '$tel_no', 
              email = '$email', 
              address = '$address' 
              WHERE id = '$supplier_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Updated Supplier: $suplier_name (ID: $supplier_id) | Contact: $contact_person | Tel: $tel_no | Email: $email | Address: $address";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Supplier', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: suplier_list.php?status=success");
    } else {
        header("Location: suplier_list.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
}
?>