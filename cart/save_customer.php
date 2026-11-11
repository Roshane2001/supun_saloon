<?php
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = mysqli_real_escape_string($con, $_POST['customer_name'] ?? '');
    // Contact number must contain exactly 10 digits (optional field)
    $contact_no = trim($_POST['contact_no'] ?? '');
    // Normalise the number so "077 123 4567" is accepted as 0771234567
    $contact_no = preg_replace('/\D+/', '', $contact_no);

    if ($contact_no !== '' && !preg_match('/^0\d{9}$/', $contact_no)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid 10-digit contact number (e.g. 0771234567).']);
        exit();
    }

    $contact_no = mysqli_real_escape_string($con, $contact_no);
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $address = mysqli_real_escape_string($con, $_POST['address'] ?? '');

    if (empty($customer_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Customer Name is required.']);
        exit();
    }

    $query = "INSERT INTO tblcustomer (customer_name, contact_no, email, address) 
              VALUES ('$customer_name', '$contact_no', '$email', '$address')";

    if (mysqli_query($con, $query)) {
        $new_customer_id = mysqli_insert_id($con);

        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $log_details = "Added Customer (POS): $customer_name | Phone: $contact_no | Email: $email";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Customer', '$ip_address', '$user_agent', '$log_details')";
        mysqli_query($con, $log_query);

        echo json_encode([
            'status' => 'success',
            'message' => 'Customer added successfully!',
            'customer_id' => $new_customer_id,
            'customer_name' => $customer_name
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding customer: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
exit();
?>