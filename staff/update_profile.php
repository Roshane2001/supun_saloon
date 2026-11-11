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
    $staff_id = mysqli_real_escape_string($con, $_POST['staff_id'] ?? '');

    // Ensure the logged-in user is only updating their own profile
    if ($staff_id == '' || $staff_id != $_SESSION['staff_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You can only update your own profile.']);
        exit();
    }

    // Load current record so omitted fields keep their existing values
    $current_res = mysqli_query($con, "SELECT staff_name, nic_no, telephone_no, address, username FROM tblstaff WHERE id = '$staff_id' LIMIT 1");
    $current = ($current_res && mysqli_num_rows($current_res) > 0) ? mysqli_fetch_assoc($current_res) : null;
    if (!$current) {
        echo json_encode(['status' => 'error', 'message' => 'User record not found in the system.']);
        exit();
    }

    // Staff name
    if (isset($_POST['staff_name'])) {
        $staff_name = trim($_POST['staff_name']);
        if ($staff_name === '') {
            echo json_encode(['status' => 'error', 'message' => 'Staff name is required.']);
            exit();
        }
    } else {
        $staff_name = $current['staff_name'];
    }
    $staff_name = mysqli_real_escape_string($con, $staff_name);

    // NIC number
    if (isset($_POST['nic_no'])) {
        $nic_no = trim($_POST['nic_no']);
        if ($nic_no === '') {
            echo json_encode(['status' => 'error', 'message' => 'NIC number is required.']);
            exit();
        }
    } else {
        $nic_no = $current['nic_no'];
    }
    $nic_no = mysqli_real_escape_string($con, $nic_no);

    // Telephone number
    if (isset($_POST['telephone_no'])) {
        $telephone_no = trim($_POST['telephone_no']);
        $telephone_no = preg_replace('/\D+/', '', $telephone_no);
        if ($telephone_no !== '' && !preg_match('/^0\d{9}$/', $telephone_no)) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a valid 10-digit telephone number (e.g. 0771234567).']);
            exit();
        }
    } else {
        $telephone_no = $current['telephone_no'] ?? '';
        $telephone_no = preg_replace('/\D+/', '', $telephone_no);
    }
    $telephone_no = mysqli_real_escape_string($con, $telephone_no);

    // Address
    if (isset($_POST['address'])) {
        $address = $_POST['address'];
    } else {
        $address = $current['address'] ?? '';
    }
    $address = mysqli_real_escape_string($con, $address);

    // Username
    if (isset($_POST['username'])) {
        $username = trim($_POST['username']);
        if ($username === '') {
            echo json_encode(['status' => 'error', 'message' => 'Username is required.']);
            exit();
        }
    } else {
        $username = $current['username'];
    }
    $username = mysqli_real_escape_string($con, $username);

    $password = $_POST['password'] ?? '';

    // Duplicate checks: NIC number and username must be unique (excluding this staff)
    $duplicate_fields = [];

    $dup_check = mysqli_query($con, "SELECT id FROM tblstaff WHERE nic_no = '$nic_no' AND id != '$staff_id' LIMIT 1");
    if ($dup_check && mysqli_num_rows($dup_check) > 0) {
        $duplicate_fields[] = 'NIC number';
    }

    $dup_check = mysqli_query($con, "SELECT id FROM tblstaff WHERE username = '$username' AND id != '$staff_id' LIMIT 1");
    if ($dup_check && mysqli_num_rows($dup_check) > 0) {
        $duplicate_fields[] = 'username';
    }

    if (!empty($duplicate_fields)) {
        echo json_encode(['status' => 'error', 'message' => 'A staff member with the same ' . implode(', ', $duplicate_fields) . ' already exists.']);
        exit();
    }

    $query = "UPDATE tblstaff SET
              staff_name = '$staff_name',
              nic_no = '$nic_no',
              telephone_no = '$telephone_no',
              address = '$address',
              username = '$username'";

    // Only update password if a new one is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = mysqli_real_escape_string($con, $hashed_password);
        $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id = '$staff_id'";

    if (mysqli_query($con, $query)) {
        // Update session values if changed
        $_SESSION['staff_name'] = $staff_name;
        $_SESSION['username'] = $username;

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