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
    $type = mysqli_real_escape_string($con, $_POST['adjustment_type']); // 'Increase' or 'Decrease'
    $qty = (float)$_POST['quantity'];
    $reason = mysqli_real_escape_string($con, $_POST['reason']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $user_id = $_SESSION['staff_id'];

    if ($qty <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than zero']);
        exit;
    }

    mysqli_begin_transaction($con);

    try {
        // Get current stock info
        $check_stock = mysqli_query($con, "SELECT quantity_on_hand, purchase_price, reorder_level FROM tblstock WHERE product_id = '$product_id'");
        if (mysqli_num_rows($check_stock) == 0) throw new Exception("Please set Opening Stock for this product first.");
        
        $current = mysqli_fetch_assoc($check_stock);
        $new_qty = ($type == 'Increase') ? $current['quantity_on_hand'] + $qty : $current['quantity_on_hand'] - $qty;

        if ($new_qty < 0) throw new Exception("Adjustment failed. Resulting stock cannot be negative.");

        // Calculate new status
        $status = 'In Stock';
        if ($new_qty == 0) $status = 'Out of Stock';
        elseif ($new_qty <= $current['reorder_level']) $status = 'Low Stock';
        
        $new_value = $new_qty * $current['purchase_price'];

        // 1. Update tblstock
        $sql_update = "UPDATE tblstock SET 
                       quantity_on_hand = '$new_qty', 
                       stock_value = '$new_value', 
                       stock_status = '$status' 
                       WHERE product_id = '$product_id'";
        if (!mysqli_query($con, $sql_update)) throw new Exception(mysqli_error($con));

        // 2. Insert into tblstock_adjustment
        $sql_adj = "INSERT INTO tblstock_adjustment (product_id, adjustment_type, quantity, reason, notes, created_by) 
                    VALUES ('$product_id', '$type', '$qty', '$reason', '$notes', '$user_id')";
        if (!mysqli_query($con, $sql_adj)) throw new Exception(mysqli_error($con));

        // 3. Insert into Movement
        $move_type = ($type == 'Increase') ? 'Adjustment Increase' : 'Adjustment Decrease';
        $q_in = ($type == 'Increase') ? $qty : 0;
        $q_out = ($type == 'Decrease') ? $qty : 0;

        $sql_move = "INSERT INTO tblstock_movement (product_id, movement_type, quantity_in, quantity_out, balance_after, unit_cost, notes, created_by) 
                     VALUES ('$product_id', '$move_type', '$q_in', '$q_out', '$new_qty', '{$current['purchase_price']}', '$reason: $notes', '$user_id')";
        if (!mysqli_query($con, $sql_move)) throw new Exception(mysqli_error($con));

        // Log activity
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        
        $p_res = mysqli_query($con, "SELECT product_name FROM tblproduct WHERE id = '$product_id'");
        $p_name = mysqli_fetch_assoc($p_res)['product_name'] ?? 'Unknown Product';
        
        $details = "Product: $p_name | Count: $qty | Type: $type (Update) | New Balance: $new_qty | Reason: $reason";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Stock Adjustment', '$ip_address', '$user_agent', '$details')";
        if (!mysqli_query($con, $log_query)) throw new Exception(mysqli_error($con));

        mysqli_commit($con);
        echo json_encode(['status' => 'success', 'message' => 'Stock adjusted successfully']);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>