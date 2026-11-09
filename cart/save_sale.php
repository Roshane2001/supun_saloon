<?php
session_start();
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

$current_cashier_id = $_SESSION['staff_id'] ?? null; // Get staff ID from session

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
    $customer_id = !empty($data['customer_id']) ? mysqli_real_escape_string($con, $data['customer_id']) : 1;
    $cart_items = $data['cart_items'];
    $total_items = (int)$data['total_items'];
    $total_qty = (float)$data['total_qty'];
    $subtotal = (float)$data['subtotal'];
    $discount_amount = (float)$data['discount_amount']; // Keep discount if you plan to add it later
    $grand_total = (float)$data['grand_total'];
    $amount_received = (float)$data['amount_received'];
    $change_amount = (float)$data['change_amount'];
    $due_amount = (float)$data['due_amount'];
    $payment_method = mysqli_real_escape_string($con, $data['payment_method']);

    // Generate Invoice Number
    $invoice_prefix = "POS-" . date("Ymd");
    $last_invoice_query = mysqli_query($con, "SELECT invoice_no FROM tblsale WHERE invoice_no LIKE '$invoice_prefix%' ORDER BY id DESC LIMIT 1");
    $last_invoice_no = mysqli_fetch_assoc($last_invoice_query)['invoice_no'] ?? '';

    $next_sequence = 1;
    if ($last_invoice_no) {
        $parts = explode('-', $last_invoice_no);
        if (count($parts) == 3) {
            $next_sequence = (int)$parts[2] + 1;
        }
    }
    $invoice_no = $invoice_prefix . '-' . str_pad($next_sequence, 4, '0', STR_PAD_LEFT);

    mysqli_begin_transaction($con);

    try {
        // 1. Insert into tblsale
        $sale_query = "INSERT INTO tblsale (invoice_no, sale_date, customer_id, total_items, total_qty, subtotal, discount_amount, grand_total, amount_received, change_amount, due_amount, payment_method, created_by)
                       VALUES ('$invoice_no', NOW(), '$customer_id', '$total_items', '$total_qty', '$subtotal', '$discount_amount', '$grand_total', '$amount_received', '$change_amount', '$due_amount', '$payment_method', " . ($current_cashier_id ? "'$current_cashier_id'" : "NULL") . ")";
        
        if (!mysqli_query($con, $sale_query)) {
            throw new Exception("Failed to save sale: " . mysqli_error($con));
        }
        $sale_id = mysqli_insert_id($con);

        // 2. Insert into tblsale_items and update tblstock & tblstock_movement
        foreach ($cart_items as $item) {
            $product_id = mysqli_real_escape_string($con, $item['product_id']);
            $product_name = mysqli_real_escape_string($con, $item['product_name']);
            $product_code = mysqli_real_escape_string($con, $item['product_code']);
            $unit_price = (float)$item['unit_price'];
            $quantity = (float)$item['quantity'];
            $line_total = (float)$item['line_total'];
            $purchase_price_at_sale = (float)$item['purchase_price'];

            // Insert into tblsale_items
            $sale_item_query = "INSERT INTO tblsale_items (sale_id, product_id, product_name, product_code, unit_price, quantity, line_total, purchase_price_at_sale)
                                VALUES ('$sale_id', '$product_id', '$product_name', '$product_code', '$unit_price', '$quantity', '$line_total', '$purchase_price_at_sale')";
            if (!mysqli_query($con, $sale_item_query)) {
                throw new Exception("Failed to save sale item: " . mysqli_error($con));
            }

            // Update tblstock
            $stock_update_query = "UPDATE tblstock SET 
                                   quantity_on_hand = quantity_on_hand - '$quantity',
                                   stock_value = (quantity_on_hand - '$quantity') * purchase_price,
                                   stock_status = CASE
                                       WHEN (quantity_on_hand - '$quantity') <= 0 THEN 'Out of Stock'
                                       WHEN (quantity_on_hand - '$quantity') <= reorder_level THEN 'Low Stock'
                                       ELSE 'In Stock'
                                   END
                                   WHERE product_id = '$product_id'";
            if (!mysqli_query($con, $stock_update_query)) {
                throw new Exception("Failed to update stock for product $product_id: " . mysqli_error($con));
            }

            // Insert into tblstock_movement
            $current_stock_query = mysqli_query($con, "SELECT quantity_on_hand FROM tblstock WHERE product_id = '$product_id'");
            $current_stock_row = mysqli_fetch_assoc($current_stock_query);
            $balance_after_sale = $current_stock_row['quantity_on_hand'];

            $movement_query = "INSERT INTO tblstock_movement (product_id, movement_type, quantity_out, balance_after, unit_cost, reference_no, notes, created_by)
                               VALUES ('$product_id', 'Sale', '$quantity', '$balance_after_sale', '$purchase_price_at_sale', '$invoice_no', 'Sold in POS transaction', " . ($current_cashier_id ? "'$current_cashier_id'" : "NULL") . ")";
            if (!mysqli_query($con, $movement_query)) {
                throw new Exception("Failed to log stock movement for product $product_id: " . mysqli_error($con));
            }
        }

        // Log activity
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $log_details = "Processed Sale. Invoice: $invoice_no | Items: $total_items | Total: LKR " . number_format($grand_total, 2) . " | Method: $payment_method";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$current_cashier_id', 'Complete Sale', '$ip_address', '$user_agent', '$log_details')";
        if (!mysqli_query($con, $log_query)) throw new Exception("Failed to log activity: " . mysqli_error($con));

        mysqli_commit($con);
        echo json_encode(['status' => 'success', 'message' => 'Sale completed successfully!', 'invoice_no' => $invoice_no]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
exit();
?>