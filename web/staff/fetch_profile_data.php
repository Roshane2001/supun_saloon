
<?php
session_start();
// Adjusting base path to reach include directory from auth folder
$base_path = '../';
include $base_path . 'include/dbconnection.php';
header('Content-Type: application/json');

// Check if the user is authorized (logged in)
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit();
}

$staff_id = mysqli_real_escape_string($con, $_SESSION['staff_id']);

// Query to fetch user details along with the role title
$query = "SELECT s.id, s.staff_name, s.nic_no, s.address, s.telephone_no, s.username, r.role as role_name, s.is_active, s.created_at 
          FROM tblstaff s 
          JOIN tblrole r ON s.role_id = r.id 
          WHERE s.id = '$staff_id' 
          LIMIT 1";

$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    // Returning success with the user data object
    echo json_encode(['status' => 'success', 'data' => $user_data]);
} else {
    // This handles cases where the session might be stale or the user was deleted
    echo json_encode(['status' => 'error', 'message' => 'User record not found in the system.']);
}

mysqli_close($con);
exit();
?>
