<?php
session_start(); // Start the session
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = ($page - 1) * $limit;

$search_term = mysqli_real_escape_string($con, $_GET['search'] ?? '');
$parent_category_id = mysqli_real_escape_string($con, $_GET['parent_category_id'] ?? '');
$sub_category_id = mysqli_real_escape_string($con, $_GET['sub_category_id'] ?? '');

$where_clauses = ["p.is_active = 1"];

if (!empty($search_term)) {
    $where_clauses[] = "(p.product_name LIKE '%$search_term%' OR p.product_code LIKE '%$search_term%')";
}
if (!empty($parent_category_id)) {
    $where_clauses[] = "p.parent_category_id = '$parent_category_id'";
}
if (!empty($sub_category_id)) {
    $where_clauses[] = "p.sub_category_id = '$sub_category_id'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

$query = "SELECT 
            p.id, 
            p.product_name, 
            p.product_code, 
            p.image,
            p.price AS selling_price,
            s.quantity_on_hand, 
            s.reorder_level, 
            s.stock_status,
            s.purchase_price, -- Include purchase price for sale_items
            pc.parent_category_name, 
            sc.sub_category_name, 
            sup.suplier_name
          FROM tblproduct p
          LEFT JOIN tblstock s ON p.id = s.product_id
          LEFT JOIN tblparent_category pc ON p.parent_category_id = pc.id
          LEFT JOIN tblsub_category sc ON p.sub_category_id = sc.id
          LEFT JOIN tblsuplier sup ON p.suplier_id = sup.id
          $where_sql
          ORDER BY p.product_name ASC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($con, $query);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode(['products' => $products]);
?>