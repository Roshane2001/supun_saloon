<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    // Sanitize inputs
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $parent_category_id = mysqli_real_escape_string($con, $_POST['parent_category_id']);
    $sub_category_id = mysqli_real_escape_string($con, $_POST['sub_category_id']);
    $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
    $product_code = mysqli_real_escape_string($con, $_POST['product_code']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $suplier_id = !empty($_POST['suplier_id']) ? "'" . mysqli_real_escape_string($con, $_POST['suplier_id']) . "'" : "NULL";

    // Handle image upload if a new file is selected
    $image_update_sql = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadFileDir = '../assets/img/products/';
        $fileName = $_FILES['product_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $dest_path)) {
            // You might want to delete the old image here as well
            $image_update_sql = ", image = '$newFileName'";
        }
    }

    // Update query
    $query = "UPDATE tblproduct SET 
              parent_category_id = '$parent_category_id', 
              sub_category_id = '$sub_category_id', 
              suplier_id = $suplier_id, 
              product_name = '$product_name', 
              product_code = '$product_code', 
              price = '$price'
              $image_update_sql
              WHERE id = '$product_id'";

    if (mysqli_query($con, $query)) {
        // Log activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
        
        $clean_sup = str_replace("'", "", $suplier_id); // Remove quotes for cleaner logging
        $details = "Updated Product: $product_name (ID: $product_id) | Code: $product_code | Category ID: $sub_category_id | Supplier ID: $clean_sup | Price: $price";

        $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent, details) 
                      VALUES ('$user_id', 'Update Product', '$ip_address', '$user_agent', '$details')";
        mysqli_query($con, $log_query);

        header("Location: appointment_list.php?status=success");
    } else {
        header("Location: appointment_list.php?status=error&message=" . urlencode(mysqli_error($con)));
    }
    exit();
}
?>