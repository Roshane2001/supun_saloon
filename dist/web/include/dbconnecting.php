<?php
date_default_timezone_set("Asia/Colombo");

// Detect Local or Live Server
if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "127.0.0.1") {

    // Local Database Configuration (XAMPP)
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "saloon_templete";

} else {

    // Live Hosting Database Configuration
    $host = "localhost";
    $username = "supunsal_admin";
    $password = "supun@321";
    $database = "supunsal_db";

}

// Create Connection
$con = mysqli_connect($host, $username, $password, $database);

// Check Connection
if (!$con) {
    die("Database Connection Failed: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, "utf8mb4");
}

// Connection Successful
?>