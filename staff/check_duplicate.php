<?php
session_start();
$base_path = '../';
include $base_path . 'include/dbconnection.php';

if (!isset($_SESSION['staff_id'])) {
    http_response_code(403);
    echo json_encode(['duplicate' => false, 'error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$field = $_GET['field'] ?? '';
$value = trim($_GET['value'] ?? '');
$exclude_id = trim($_GET['exclude_id'] ?? '');

$allowed = [
    'nic_no' => 'nic_no',
    'username' => 'username',
    'telephone_no' => 'telephone_no',
];

if (!isset($allowed[$field])) {
    echo json_encode(['duplicate' => false]);
    exit();
}

$column = $allowed[$field];

if ($field === 'telephone_no') {
    $value = preg_replace('/\D+/', '', $value);
}

if ($value === '') {
    echo json_encode(['duplicate' => false]);
    exit();
}

$safe_value = mysqli_real_escape_string($con, $value);
$safe_exclude = mysqli_real_escape_string($con, $exclude_id);

$query = "SELECT id FROM tblstaff WHERE $column = '$safe_value'";
if ($safe_exclude !== '') {
    $query .= " AND id != '$safe_exclude'";
}
$query .= " LIMIT 1";

$result = mysqli_query($con, $query);
$duplicate = ($result && mysqli_num_rows($result) > 0);

echo json_encode(['duplicate' => $duplicate]);
exit();
?>