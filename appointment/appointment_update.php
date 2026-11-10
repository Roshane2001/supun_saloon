
<?php
session_start();

include '../include/dbconnection.php';

// Check staff login
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Check form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment'])) {

    // Get form data
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    $service_id = intval($_POST['service_id'] ?? 0);

    $customer_name = trim($_POST['customer_name'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $status = isset($_POST['status']) ? intval($_POST['status']) : -1;
    $note = trim($_POST['note'] ?? '');

    // Validate required fields
    if (
        $appointment_id <= 0 ||
        $service_id <= 0 ||
        $customer_name === '' ||
        $date === '' ||
        $time === '' ||
        !in_array($status, [0, 1, 2, 3], true)
    ) {
        header("Location: appointment_list.php?status=error&message=" . urlencode("Please enter valid appointment details."));
        exit();
    }

    // Validate date
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        header("Location: appointment_list.php?status=error&message=" . urlencode("Invalid appointment date."));
        exit();
    }

    // Validate time
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
        header("Location: appointment_list.php?status=error&message=" . urlencode("Invalid appointment time."));
        exit();
    }

    // Check whether service exists and is active
    $service_check = mysqli_prepare(
        $con,
        "SELECT id FROM servicers WHERE id = ? AND status = 1"
    );

    mysqli_stmt_bind_param($service_check, "i", $service_id);
    mysqli_stmt_execute($service_check);

    $service_result = mysqli_stmt_get_result($service_check);

    if (mysqli_num_rows($service_result) === 0) {
        mysqli_stmt_close($service_check);

        header("Location: appointment_list.php?status=error&message=" . urlencode("Selected service is unavailable."));
        exit();
    }

    mysqli_stmt_close($service_check);

    // Update appointment
    $query = "UPDATE tblappointment SET
                service_id = ?,
                customer_name = ?,
                date = ?,
                time = ?,
                status = ?,
                note = ?
              WHERE id = ?";

    $stmt = mysqli_prepare($con, $query);

    if (!$stmt) {
        header("Location: appointment_list.php?status=error&message=" . urlencode(mysqli_error($con)));
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt,
        "isssisi",
        $service_id,
        $customer_name,
        $date,
        $time,
        $status,
        $note,
        $appointment_id
    );

    if (mysqli_stmt_execute($stmt)) {

        // Log user activity
        $user_id = $_SESSION['staff_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $details = "Updated Appointment ID: $appointment_id | Customer: $customer_name | Service ID: $service_id | Date: $date | Time: $time | Status: $status";

        $log_stmt = mysqli_prepare(
            $con,
            "INSERT INTO tbluser_activity
            (user_id, action, ip_address, user_agent, details)
            VALUES (?, 'Update Appointment', ?, ?, ?)"
        );

        if ($log_stmt) {
            mysqli_stmt_bind_param(
                $log_stmt,
                "isss",
                $user_id,
                $ip_address,
                $user_agent,
                $details
            );

            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }

        mysqli_stmt_close($stmt);

        header("Location: appointment_list.php?status=success");
        exit();

    } else {

        $error = mysqli_stmt_error($stmt);

        mysqli_stmt_close($stmt);

        header("Location: appointment_list.php?status=error&message=" . urlencode($error));
        exit();
    }

} else {

    header("Location: appointment_list.php");
    exit();
}
?>
