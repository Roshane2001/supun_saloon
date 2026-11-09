<?php
session_start(); // Start the session
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Fetch items and stock info
    $items_query = mysqli_query($con, "SELECT hi.*, s.quantity_on_hand as max_stock FROM tblheld_sale_items hi JOIN tblstock s ON hi.product_id = s.product_id WHERE hi.held_sale_id = '$id'");
    $items = [];
    while ($row = mysqli_fetch_assoc($items_query)) { $items[] = $row; }
    
    $sale_query = mysqli_query($con, "SELECT hold_reference, customer_id FROM tblheld_sales WHERE id = '$id'");
    $sale = mysqli_fetch_assoc($sale_query);

    // Log activity
    $user_id = $_SESSION['staff_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
    $log_details = "Resumed Held Sale. Reference: " . ($sale['hold_reference'] ?? 'Unknown');
    $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                  VALUES ('$user_id', 'Resume Sale', '$ip_address', '$user_agent', '$log_details')";
    mysqli_query($con, $log_query);

    // Important: Delete the held record once it is resumed to prevent duplicates
    mysqli_query($con, "DELETE FROM tblheld_sales WHERE id = '$id'");

    echo json_encode(['status' => 'success', 'items' => $items, 'customer_id' => $sale['customer_id']]);
} else {
    // List all held sales
    $query = mysqli_query($con, "SELECT h.*, c.customer_name FROM tblheld_sales h LEFT JOIN tblcustomer c ON h.customer_id = c.id ORDER BY hold_date DESC");
    $sales = [];
    while ($row = mysqli_fetch_assoc($query)) { $sales[] = $row; }
    echo json_encode(['sales' => $sales]);
}
exit();