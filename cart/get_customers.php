<?php
session_start();
include '../include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$search_term = mysqli_real_escape_string($con, $_GET['q'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$limit = 10; // Number of results per page
$offset = ($page - 1) * $limit;

$where_clause = "WHERE is_active = 1";
if (!empty($search_term)) {
    $where_clause .= " AND (customer_name LIKE '%$search_term%' OR contact_no LIKE '%$search_term%')";
}

// Query to get total count for pagination
$count_query = "SELECT COUNT(id) AS total_count FROM tblcustomer $where_clause";
$count_result = mysqli_query($con, $count_query);
$total_count = mysqli_fetch_assoc($count_result)['total_count'];

// Query to get customer data
$query = "SELECT id, customer_name, contact_no FROM tblcustomer $where_clause ORDER BY customer_name ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($con, $query);

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = [
        'id' => $row['id'],
        'text' => htmlspecialchars($row['customer_name']),
        'contact_no' => htmlspecialchars($row['contact_no']) // Include contact_no for display in templateResult
    ];
}

// Ensure "Walk-in Customer" (ID 1) is always available if not already in results and search is empty
if (empty($search_term) && $page === 1) {
    $walk_in_exists = false;
    foreach ($customers as $customer) {
        if ($customer['id'] == 1) { $walk_in_exists = true; break; }
    }
    if (!$walk_in_exists) {
        array_unshift($customers, ['id' => 1, 'text' => 'Walk-in Customer', 'contact_no' => '']);
        $total_count++; // Adjust total count
    }
}

echo json_encode([
    'items' => $customers,
    'total_count' => $total_count
]);
?>