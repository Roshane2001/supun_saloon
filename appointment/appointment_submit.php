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
    // Telephone number must contain exactly 10 digits (optional field)
    $telephone_number = trim($_POST['telephone_number'] ?? '');
    // Normalise the number so "077 123 4567" is accepted as 0771234567
    $telephone_number = preg_replace('/\D+/', '', $telephone_number);

    if ($telephone_number !== '' && !preg_match('/^0\d{9}$/', $telephone_number)) {
        header("Location: appointment_add.php?status=error&message=" . urlencode("Please enter a valid 10-digit telephone number (e.g. 0771234567)."));
        exit();
    }

    $telephone_number = mysqli_real_escape_string($con, $telephone_number);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $time = mysqli_real_escape_string($con, $_POST['time']);
    $note = mysqli_real_escape_string($con, trim($_POST['note'] ?? ''));


    // Appointments can only be scheduled for today or a future date
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        header("Location: appointment_add.php?status=error&message=" . urlencode("Invalid appointment date."));
        exit();
    }

    if ($date < date('Y-m-d')) {
        header("Location: appointment_add.php?status=error&message=" . urlencode("Please select today or a future date for the appointment."));
        exit();
    }

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