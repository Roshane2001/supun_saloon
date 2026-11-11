<?php
session_start();

include __DIR__ . '/../../include/dbconnecting.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {

    // Get form data
    $customer_name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone_number = trim($_POST['telephone_number'] ?? '');
    $service_id = (int)($_POST['service_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    $preferred_time = $_POST['preferred_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    // Normalise the phone number so "077 123 4567" is accepted as 0771234567
    $telephone_number = preg_replace('/\D+/', '', $telephone_number);

    // Validate required fields (email is optional)
    if (
        empty($customer_name) ||
        empty($telephone_number) ||
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

    // Phone number must be a 10-digit Sri Lankan number, e.g. 0771234567
    if (!preg_match('/^0\d{9}$/', $telephone_number)) {
        echo "<script>
            alert('Please enter a valid 10-digit phone number (e.g. 0771234567).');
            window.history.back();
        </script>";
        exit();
    }

    // Email is optional, but must be a valid address when provided
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Please enter a valid email address, or leave the email field empty.');
            window.history.back();
        </script>";
        exit();
    }

    // Appointments can only be booked for today or a future date
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
        echo "<script>
            alert('Please select today or a future date.');
            window.history.back();
        </script>";
        exit();
    }

    // 1. Check whether customer already exists
    $check_customer = mysqli_prepare(
        $con,
        "SELECT id FROM tblcustomer WHERE contact_no = ? LIMIT 1"
    );

    if (!$check_customer) {
        die("Database error: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param(
        $check_customer,
        "s",
        $telephone_number
    );

    mysqli_stmt_execute($check_customer);

    $customer_result = mysqli_stmt_get_result($check_customer);

    // 2. Register customer if not already exists
    if ($customer = mysqli_fetch_assoc($customer_result)) {

        // Existing customer
        $customer_id = $customer['id'];

    } else {

        // Insert new customer
        $insert_customer = mysqli_prepare(
            $con,
            "INSERT INTO tblcustomer
            (customer_name, contact_no, email)
            VALUES (?, ?, ?)"
        );

        if (!$insert_customer) {
            die("Database error: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param(
            $insert_customer,
            "sss",
            $customer_name,
            $telephone_number,
            $email
        );

        if (!mysqli_stmt_execute($insert_customer)) {

            echo "<script>
                alert('Customer registration failed. Please try again.');
                window.history.back();
            </script>";
            exit();
        }

        $customer_id = mysqli_insert_id($con);

        mysqli_stmt_close($insert_customer);
    }

    mysqli_stmt_close($check_customer);

    // 3. Insert appointment
    $insert_appointment = mysqli_prepare(
        $con,
        "INSERT INTO tblappointment
        (service_id, customer_name, telephone_number, date, time, note)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$insert_appointment) {
        die("Database error: " . mysqli_error($con));
    }

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

    if (mysqli_stmt_execute($insert_appointment)) {


    $booking_id = mysqli_insert_id($con);
        // Booking successful
        echo "<script>
            alert('Your booking request has been submitted successfully!');
            window.location.href = '../booking_confirm.php?booking_id=' + $booking_id;
        </script>";
        exit();

    } else {

        // Booking failed
        echo "<script>
            alert('Booking submission failed. Please try again.');
            window.history.back();
        </script>";
        exit();
    }

    mysqli_stmt_close($insert_appointment);

} else {

    // Redirect if accessed without form submission
    header("Location: index.php");
    exit();
}
?>
