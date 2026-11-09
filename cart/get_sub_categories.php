<?php
session_start(); // Start the session
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_POST['parent_id'])) {
    $parent_id = mysqli_real_escape_string($con, $_POST['parent_id']);
    
    // Fetch active sub-categories for the selected parent
    $query = mysqli_query($con, "SELECT id, sub_category_name FROM tblsub_category WHERE parent_category_id = '$parent_id' AND is_active = 1 ORDER BY sub_category_name ASC");
    
    echo '<option value="">Select Sub Category</option>';
    while ($row = mysqli_fetch_assoc($query)) {
        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['sub_category_name']) . '</option>';
    }
}
?>