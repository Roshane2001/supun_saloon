<?php
session_start();
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

$current_cashier_id = $_SESSION['staff_id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
    $customer_id = mysqli_real_escape_string($con, $data['customer_id']);
    $reference = mysqli_real_escape_string($con, $data['hold_reference']);
    $total_items = (int)$data['total_items'];
    $total_qty = (float)$data['total_qty'];
    $subtotal = (float)$data['subtotal'];
    $grand_total = (float)$data['grand_total'];

    mysqli_begin_transaction($con);

    try {
        $query = "INSERT INTO tblheld_sales (hold_reference, customer_id, total_items, total_qty, subtotal, grand_total, created_by)
                  VALUES ('$reference', '$customer_id', '$total_items', '$total_qty', '$subtotal', '$grand_total', " . ($current_cashier_id ? "'$current_cashier_id'" : "NULL") . ")";
        
        if (!mysqli_query($con, $query)) throw new Exception("Error saving held sale: " . mysqli_error($con));
        $held_id = mysqli_insert_id($con);

        foreach ($data['cart_items'] as $item) {
            $p_id = mysqli_real_escape_string($con, $item['product_id']);
            $p_name = mysqli_real_escape_string($con, $item['product_name']);
            $p_code = mysqli_real_escape_string($con, $item['product_code']);
            $price = (float)$item['unit_price'];
            $purchase_price = (float)$item['purchase_price'];
            $qty = (float)$item['quantity'];
            $line_total = (float)$item['line_total'];

            $item_query = "INSERT INTO tblheld_sale_items (held_sale_id, product_id, product_name, product_code, unit_price, purchase_price, quantity, line_total)
                           VALUES ('$held_id', '$p_id', '$p_name', '$p_code', '$price', '$purchase_price', '$qty', '$line_total')";
            if (!mysqli_query($con, $item_query)) throw new Exception("Error saving held item");
        }

        // Log activity
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $log_details = "Held Sale. Reference: $reference | Items: $total_items | Total: LKR " . number_format($grand_total, 2);
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$current_cashier_id', 'Hold Sale', '$ip_address', '$user_agent', '$log_details')";
        if (!mysqli_query($con, $log_query)) throw new Exception("Failed to log activity");

        mysqli_commit($con);
        echo json_encode(['status' => 'success', 'message' => 'Sale held successfully!']);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
exit();