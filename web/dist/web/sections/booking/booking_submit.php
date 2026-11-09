<?php
session_start();

include __DIR__ . '/../../include/dbconnecting.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {

    // Get form data
    $customer_name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone_number = trim($_POST['telephone_number'] ?? '');
    $service_id = ($_POST['service_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    $preferred_time = $_POST['preferred_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    // Validate required fields
    if (
        empty($customer_name) ||
        empty($telephone_number) ||
        empty($email) ||
        $service_id <= 0 ||
        empty($date) ||
        empty($preferred_time)
    ) {
        echo "<script>
            alert('Please fill in all required fields.');
            window.history.back();
        </script>";
        exit();
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {

        // 1. Check whether customer already exists using contact number
        $check_customer = mysqli_prepare(
            $con,
            "SELECT id FROM tblcustomer WHERE contact_no = ? LIMIT 1"
        );

        mysqli_stmt_bind_param($check_customer, "s", $telephone_number);
        mysqli_stmt_execute($check_customer);

        $customer_result = mysqli_stmt_get_result($check_customer);

        if ($customer = mysqli_fetch_assoc($customer_result)) {

            // Existing customer
            $customer_id = $customer['id'];

        } else {

            //Insert new customer
            $insert_customer = mysqli_prepare(
                $con,
                "INSERT INTO tblcustomer 
                (customer_name, contact_no, email)
                VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $insert_customer,
                "sss",
                $customer_name,
                $telephone_number,
                $email
            );

            if (!mysqli_stmt_execute($insert_customer)) {
                throw new Exception("Customer registration failed.");
            }

            $customer_id = mysqli_insert_id($con);
        }

        //Insert appointment
        $insert_appointment = mysqli_prepare(
            $con,
            "INSERT INTO tblappointment
            (service_id, customer_name, telephone_number, date, time, note)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insert_appointment,
            "isssss",
            $service_id,
            $customer_name,
            $telephone_number,
            $date,
            $preferred_time,
            $notes
        );

        if (!mysqli_stmt_execute($insert_appointment)) {
            throw new Exception("Booking submission failed.");
        }

        // Commit transaction
        mysqli_commit($con);

        echo "<script>
            alert('Your booking request has been submitted successfully!');
            window.location.href = 'web/index.php';
        </script>";
        exit();

    } catch (Exception $e) {

        // Rollback if something goes wrong
        mysqli_rollback($con);

        error_log($e->getMessage());

        echo "<script>
            alert('Something went wrong. Please try again.');
            window.history.back();
        </script>";
        exit();
    }

} else {

    header("Location: booking.php");
    exit();
}
?>
