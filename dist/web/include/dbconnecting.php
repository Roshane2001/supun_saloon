<?php
date_default_timezone_set("Asia/Colombo");

// Database Configuration
$host = "localhost";
$username = "supunsal_admin";
$password = "supun@321";
$database = "supunsal_db";

// Create Connection
$con = mysqli_connect($host, $username, $password, $database);

// Check Connection
if (!$con) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set Character Encoding
mysqli_set_charset($con, "utf8mb4");

// Connection Successful
?>
