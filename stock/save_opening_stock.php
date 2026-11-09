<?php
session_start(); // Start the session
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $qty = (float)$_POST['quantity'];
    $price = (float)$_POST['purchase_price'];
    $reorder = (float)$_POST['reorder_level'];
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $user_id = $_SESSION['staff_id'];
    
    if ($qty < 0 || $price < 0) {
        echo json_encode(['status' => 'error', 'message' => 'Values cannot be negative']);
        exit;
    }

    // Calculate Status
    $status = 'In Stock';
    if ($qty == 0) $status = 'Out of Stock';
    elseif ($qty <= $reorder) $status = 'Low Stock';
    
    $value = $qty * $price;

    mysqli_begin_transaction($con);

    try {
        // 1. Insert or Update tblstock
        $sql_stock = "INSERT INTO tblstock (product_id, opening_stock, quantity_on_hand, reorder_level, purchase_price, stock_value, stock_status) 
                      VALUES ('$product_id', '$qty', '$qty', '$reorder', '$price', '$value', '$status')
                      ON DUPLICATE KEY UPDATE 
                      opening_stock = '$qty', quantity_on_hand = '$qty', reorder_level = '$reorder', 
                      purchase_price = '$price', stock_value = '$value', stock_status = '$status'";
        
        if (!mysqli_query($con, $sql_stock)) throw new Exception(mysqli_error($con));

        // 2. Insert into Movement
        $sql_move = "INSERT INTO tblstock_movement (product_id, movement_type, quantity_in, balance_after, unit_cost, notes, created_by) 
                     VALUES ('$product_id', 'Opening Stock', '$qty', '$qty', '$price', '$notes', '$user_id')";
        
        if (!mysqli_query($con, $sql_move)) throw new Exception(mysqli_error($con));

        // Log activity
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $p_res = mysqli_query($con, "SELECT product_name FROM tblproduct WHERE id = '$product_id'");
        $p_name = mysqli_fetch_assoc($p_res)['product_name'] ?? 'Unknown Product';
        $details = "Product: $p_name | Count: $qty | Type: Opening Stock (Initial) | Unit Cost: $price | Reorder Level: $reorder";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Set Opening Stock', '$ip_address', '$user_agent', '$details')";
        if (!mysqli_query($con, $log_query)) throw new Exception(mysqli_error($con));

        mysqli_commit($con);
        echo json_encode(['status' => 'success', 'message' => 'Stock initialized successfully']);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>