<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    // Sanitize inputs
    $parent_category_id = mysqli_real_escape_string($con, $_POST['parent_category_id']);
    $sub_category_id = mysqli_real_escape_string($con, $_POST['sub_category_id']);
    $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
    $product_code = mysqli_real_escape_string($con, $_POST['product_code']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $suplier_id = !empty($_POST['suplier_id']) ? "'" . mysqli_real_escape_string($con, $_POST['suplier_id']) . "'" : "NULL";

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadFileDir = '../assets/img/products/';
        
        // Ensure the upload directory exists
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Generate a unique name to avoid overwriting existing files
        $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_name = $newFileName;
        }
    }

    // Insert into tblproduct
    $query = "INSERT INTO tblproduct (parent_category_id, sub_category_id, suplier_id, product_name, product_code, price, image) 
              VALUES ('$parent_category_id', '$sub_category_id', $suplier_id, '$product_name', '$product_code', '$price', '$image_name')";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        $details = "Added product: $product_name (Code: $product_code)";
        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Add Product', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: product_add.php?status=success");
    } else {
        header("Location: product_add.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
} else {
    header("Location: product_add.php");
    exit();
}
?>