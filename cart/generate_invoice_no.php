<?php
session_start(); // Start the session
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Generate a simple invoice number (e.g., POS-YYYYMMDD-XXXX)
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
$new_invoice_no = $invoice_prefix . '-' . str_pad($next_sequence, 4, '0', STR_PAD_LEFT);

echo json_encode(['invoice_no' => $new_invoice_no]);
exit();
?>