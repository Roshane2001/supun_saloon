<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_appointment'])) {
    // Sanitize inputs
    
    $service_id = mysqli_real_escape_string($con, $_POST['service_id']);
    $customer_name = mysqli_real_escape_string($con, $_POST['customer_name']);
    $telephone_number = mysqli_real_escape_string($con, $_POST['telephone_number']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $time = mysqli_real_escape_string($con, $_POST['time']);
    $note = mysqli_real_escape_string($con, $_POST['note']);


    // Insert into tblappointment
    $query = "INSERT INTO tblappointment (service_id, customer_name, telephone_number, date, time, note) 
              VALUES ('$service_id', '$customer_name', '$telephone_number', '$date', '$time', '$note')";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Added appointment for: $customer_name";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Appointment', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: appointment_add.php?status=success");
    } else {
        header("Location: appointment_add.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
} else {
    header("Location: appointment_add.php");
    exit();
}
?>