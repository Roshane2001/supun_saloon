<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_POST['save_branding'])) {
    // Basic Input Sanitization
    $company_name = mysqli_real_escape_string($con, $_POST['company_name']);
    $company_email = mysqli_real_escape_string($con, $_POST['company_email']);
    $telephone = trim($_POST['telephone'] ?? '');
    // Normalise the phone number so "011 234 5678" is accepted as 0112345678
    $telephone = preg_replace('/\D+/', '', $telephone);
    // Validate 10-digit phone number when provided
    if ($telephone !== '' && !preg_match('/^0\d{9}$/', $telephone)) {
        header("Location: branding.php?status=error&message=" . urlencode("Please enter a valid 10-digit telephone number (e.g. 0112345678)."));
        exit();
    }
    $telephone = mysqli_real_escape_string($con, $telephone);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    // Prepare upload directory
    $upload_dir = "../assets/img/branding/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check for existing record
    $check_query = mysqli_query($con, "SELECT * FROM branding LIMIT 1");
    $existing = mysqli_fetch_assoc($check_query);

    // Handle Logo Upload
    $logo_name = $existing['company_logo'] ?? '';
    if (!empty($_FILES['company_logo']['name'])) {
        $logo_ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
        $logo_name = "logo_" . time() . "." . $logo_ext;
        $logo_tmp = $_FILES['company_logo']['tmp_name'];
        
        if (move_uploaded_file($logo_tmp, $upload_dir . $logo_name)) {
            // Delete old logo if exists
            if (!empty($existing['company_logo']) && file_exists($upload_dir . $existing['company_logo'])) {
                unlink($upload_dir . $existing['company_logo']);
            }
        }
    }

    // Handle Favicon Upload
    $favicon_name = $existing['company_favicon'] ?? '';
    if (!empty($_FILES['company_favicon']['name'])) {
        $favicon_ext = pathinfo($_FILES['company_favicon']['name'], PATHINFO_EXTENSION);
        $favicon_name = "favicon_" . time() . "." . $favicon_ext;
        $favicon_tmp = $_FILES['company_favicon']['tmp_name'];

        if (move_uploaded_file($favicon_tmp, $upload_dir . $favicon_name)) {
            // Delete old favicon if exists
            if (!empty($existing['company_favicon']) && file_exists($upload_dir . $existing['company_favicon'])) {
                unlink($upload_dir . $existing['company_favicon']);
            }
        }
    }

    if ($existing) {
        // Update existing record
        $id = $existing['id'];
        $sql = "UPDATE branding SET 
                company_name='$company_name', 
                company_email='$company_email', 
                telephone='$telephone', 
                address='$address', 
                company_logo='$logo_name', 
                company_favicon='$favicon_name' 
                WHERE id='$id'";
    } else {
        // Insert new record
        $sql = "INSERT INTO branding (company_name, company_email, telephone, address, company_logo, company_favicon) 
                VALUES ('$company_name', '$company_email', '$telephone', '$address', '$logo_name', '$favicon_name')";
    }

    $result = mysqli_query($con, $sql);

    if ($result) {
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Branding', '$ip_address', '$user_agent', 'Updated company branding settings')";
        mysqli_query($con, $log_query);
    }

    header("Location: branding.php?status=" . ($result ? "success" : "error"));
    exit();
}