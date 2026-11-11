<?php
session_start();
$base_path = '../';
include $base_path . 'include/dbconnection.php';
header('Content-Type: application/json');

// Check if user is logged in and if the request is a POST
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = mysqli_real_escape_string($con, $_POST['staff_id']);
    $staff_name = mysqli_real_escape_string($con, $_POST['staff_name']);
    // Telephone number must contain exactly 10 digits (optional field)
    $telephone_no = trim($_POST['telephone_no'] ?? '');
    // Normalise the number so "077 123 4567" is accepted as 0771234567
    $telephone_no = preg_replace('/\D+/', '', $telephone_no);

    if ($telephone_no !== '' && !preg_match('/^0\d{9}$/', $telephone_no)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid 10-digit telephone number (e.g. 0771234567).']);
        exit();
    }

    $telephone_no = mysqli_real_escape_string($con, $telephone_no);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $password = $_POST['password']; // New password, if provided

    // Ensure the logged-in user is only updating their own profile
    if ($staff_id != $_SESSION['staff_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You can only update your own profile.']);
        exit();
    }

    $query = "UPDATE tblstaff SET
              staff_name = '$staff_name',
              telephone_no = '$telephone_no',
              address = '$address'";

    // Only update password if a new one is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id = '$staff_id'";

    if (mysqli_query($con, $query)) {
        // Update session name if staff_name changed
        $_SESSION['staff_name'] = $staff_name;

        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Updated personal profile details";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Profile', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating profile: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
exit();
?>