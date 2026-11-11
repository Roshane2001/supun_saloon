<?php
session_start();

$base_path = '../';
include $base_path . 'include/dbconnection.php';

// Check if the user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Log page access for auditing
$user_id = $_SESSION['staff_id'];
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT'] ?? '');
$staff_name = $_SESSION['staff_name'] ?? 'Unknown';

$details = "Staff member " . $staff_name . " accessed the Appointment List directory.";

$log_query = "INSERT INTO tbluser_activity 
              (user_id, action, ip_address, user_agent, details) 
              VALUES ('$user_id', 'Accessed Appointment List Page', '$ip_address', '$user_agent', '$details')";

mysqli_query($con, $log_query);


// =====================================
// APPOINTMENT DAY FILTER
// =====================================

$selected_date = $_GET['appointment_date'] ?? '';

// Validate selected date
if (!empty($selected_date)) {

    $date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $selected_date) {
        $selected_date = '';
    }
}


// =====================================
// FETCH APPOINTMENT DATA
// =====================================

if (!empty($selected_date)) {

    $stmt = mysqli_prepare($con, "
        SELECT tblappointment.*,
               servicers.servicer_name AS service_name
        FROM tblappointment
        LEFT JOIN servicers 
            ON tblappointment.service_id = servicers.id
        WHERE tblappointment.date = ?
        ORDER BY tblappointment.time ASC
    ");

    mysqli_stmt_bind_param($stmt, "s", $selected_date);
    mysqli_stmt_execute($stmt);

    $product_query = mysqli_stmt_get_result($stmt);

} else {

    $product_query = mysqli_query($con, "
        SELECT tblappointment.*,
               servicers.servicer_name AS service_name
        FROM tblappointment
        LEFT JOIN servicers 
            ON tblappointment.service_id = servicers.id
        ORDER BY tblappointment.date DESC, tblappointment.time DESC
    ");
}

$product_data = [];

if ($product_query) {
    while ($row = mysqli_fetch_assoc($product_query)) {
        $product_data[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../include/header.php'; ?>
</head>

<body>

    <div class="wrapper">

        <!-- Sidebar -->
        <?php include '../include/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">

            <div class="main-header">

                <div class="main-header-logo">
                    <?php include '../include/logo_header.php'; ?>
                </div>

                <!-- Navbar -->
                <?php include '../include/topbar.php'; ?>

            </div>


            <div class="container">

                <div class="page-inner">

                    <div class="row">

                        <div class="col-md-12">

                            <div class="card">

                                <!-- Card Header -->
                                <div class="card-header d-flex justify-content-between align-items-center">

                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Appointment List</h4>
                                    </div>

                                </div>


                                <div class="card-body">

                                    <!-- Status Alerts -->
                                    <?php if (isset($_GET['status'])): ?>

                                    <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                                        role="alert">

                                        <?php
                                            echo $_GET['status'] == 'success'
                                                ? 'Operation successful!'
                                                : 'Error: ' . htmlspecialchars($_GET['message'] ?? 'Unknown error');
                                            ?>

                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close">
                                        </button>

                                    </div>

                                    <?php endif; ?>


                                    <!-- ================================= -->
                                    <!-- APPOINTMENT DAY FILTER -->
                                    <!-- ================================= -->

                                    <div class="row mb-4">

                                        <div class="col-md-5">

                                            <form method="GET" action="appointment_list.php">

                                                <label class="form-label fw-bold">
                                                    Filter Appointments by Day
                                                </label>

                                                <div class="input-group">

                                                    <input type="date" name="appointment_date" class="form-control"
                                                        value="<?php echo htmlspecialchars($selected_date); ?>">

                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-filter"></i> Filter
                                                    </button>

                                                    <a href="appointment_list.php" class="btn btn-secondary">
                                                        <i class="fa fa-refresh"></i> All Days
                                                    </a>

                                                </div>

                                            </form>

                                        </div>

                                    </div>


                                    <!-- Selected Date Information -->
                                    <?php if (!empty($selected_date)): ?>

                                    <div class="alert alert-info py-2">

                                        <i class="fa fa-calendar"></i>

                                        Showing appointments for:

                                        <strong>
                                            <?php echo date('d M Y', strtotime($selected_date)); ?>
                                        </strong>

                                        <span class="ms-2">
                                            | Total Appointments:
                                            <strong><?php echo count($product_data); ?></strong>
                                        </span>

                                    </div>

                                    <?php endif; ?>


                                    <!-- ================================= -->
                                    <!-- APPOINTMENT TABLE -->
                                    <!-- ================================= -->

                                    <div class="table-responsive">

                                        <table id="add-row" class="display table table-striped table-hover">

                                            <thead>

                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Telephone Number</th>
                                                    <th>Service</th>
                                                    <th>Date &amp; Time</th>

                                                    <th>Status</th>
                                                    <th style="width: 15%">Action</th>
                                                </tr>

                                            </thead>


                                            <tbody>

                                                <?php foreach ($product_data as $product): ?>

                                                <tr>

                                                    <td>
                                                        <?php echo htmlspecialchars($product['id']); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($product['customer_name']); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($product['telephone_number'] ?? 'N/A'); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($product['service_name'] ?? 'N/A'); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars(trim(($product['date'] ?? '') . ' ' . ($product['time'] ?? '')) ?: 'N/A'); ?>
                                                    </td>



                                                    <!-- Appointment Status -->
                                                    <td>

                                                        <?php

                                                            $st = $product['status'] ?? null;

                                                            if ($st === 0 || $st === '0') {

                                                                echo '<span class="badge bg-danger">Cancel</span>';

                                                            } elseif ($st === 1 || $st === '1') {

                                                                echo '<span class="badge bg-warning">Pending</span>';

                                                            } elseif ($st === 2 || $st === '2') {

                                                                echo '<span class="badge bg-info">Confirm</span>';

                                                            } elseif ($st === 3 || $st === '3') {

                                                                echo '<span class="badge bg-success">Complete</span>';

                                                            } else {

                                                                echo '<span class="badge bg-secondary">N/A</span>';

                                                            }

                                                            ?>

                                                    </td>


                                                    <!-- Action -->
                                                    <td>
                                                        <div class="form-button-action gap-2 align-items-center">

                                                        <button type="button"
                                                            class="btn btn-info btn-view-appointment"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewAppointmentModal"
                                                            title="View appointment details"
                                                            data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                                            data-customer-name="<?php echo htmlspecialchars($product['customer_name'], ENT_QUOTES); ?>"
                                                            data-telephone="<?php echo htmlspecialchars($product['telephone_number'] ?? '', ENT_QUOTES); ?>"
                                                            data-service-name="<?php echo htmlspecialchars($product['service_name'] ?? '', ENT_QUOTES); ?>"
                                                            data-date="<?php echo htmlspecialchars($product['date'] ?? ''); ?>"
                                                            data-time="<?php echo htmlspecialchars($product['time'] ?? ''); ?>"
                                                            data-status="<?php echo htmlspecialchars($product['status'] ?? ''); ?>"
                                                            data-note="<?php echo htmlspecialchars($product['note'] ?? '', ENT_QUOTES); ?>">

                                                            <i class="fas fa-eye"></i> View
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-primary btn-edit-appointment"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editAppointmentModal"
                                                            data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                                            data-service-id="<?php echo htmlspecialchars($product['service_id']); ?>"
                                                            data-customer-name="<?php echo htmlspecialchars($product['customer_name'], ENT_QUOTES); ?>"
                                                            data-telephone="<?php echo htmlspecialchars($product['telephone_number'] ?? '', ENT_QUOTES); ?>"
                                                            data-date="<?php echo htmlspecialchars($product['date'] ?? ''); ?>"
                                                            data-time="<?php echo htmlspecialchars($product['time'] ?? ''); ?>"
                                                            data-status="<?php echo htmlspecialchars($product['status']); ?>"
                                                            data-note="<?php echo htmlspecialchars($product['note'] ?? '', ENT_QUOTES); ?>">

                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>

                                                        </div>
                                                    </td>

                                                </tr>

                                                <?php endforeach; ?>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Footer -->
            <?php include '../include/footer.php'; ?>

        </div>

    </div>


    <!-- Scripts -->
    <?php include "../include/script.php"; ?>


    <!-- Edit Appointment Modal -->
    <?php include 'appointment_edit.php'; ?>


    <!-- View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1"
        role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <span class="fw-mediumbold">View</span>
                        <span class="fw-light">Appointment</span>
                    </h5>

                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">

                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%">Appointment ID</th>
                                <td id="view_id"></td>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <td id="view_customer_name"></td>
                            </tr>
                            <tr>
                                <th>Telephone Number</th>
                                <td id="view_telephone"></td>
                            </tr>
                            <tr>
                                <th>Service</th>
                                <td id="view_service"></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td id="view_date"></td>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <td id="view_time"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="view_status"></td>
                            </tr>
                            <tr>
                                <th>Note</th>
                                <td id="view_note"></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>


    <script>
    $(document).ready(function() {

        // Initialize DataTable
        $('#add-row').DataTable({
            pageLength: 10,
            order: []
        });

        // Populate Edit Appointment Modal
        $(document).on('click', '.btn-edit-appointment', function() {

            var btn = $(this);

            $('#edit_appointment_id').val(btn.attr('data-id'));

            $('#edit_service_id').val(btn.attr('data-service-id'));

            $('#edit_customer_name').val(btn.attr('data-customer-name'));

            $('#edit_telephone_number').val(btn.attr('data-telephone'));

            // Past appointments can still be edited, but a new date cannot be in the past
            var today = new Date().toISOString().split('T')[0];
            var appointmentDate = btn.attr('data-date');

            $('#edit_date').val(appointmentDate);
            $('#edit_date').attr('min', !appointmentDate || appointmentDate < today ? today : appointmentDate);

            $('#edit_time').val(btn.attr('data-time'));

            $('#edit_status').val(btn.attr('data-status'));

            $('#edit_note').val(btn.attr('data-note'));

        });

        // Populate View Appointment Modal
        $(document).on('click', '.btn-view-appointment', function() {

            var btn = $(this);
            var status = btn.attr('data-status');

            var statusLabels = {
                '0': '<span class="badge bg-danger">Cancel</span>',
                '1': '<span class="badge bg-warning">Pending</span>',
                '2': '<span class="badge bg-info">Confirm</span>',
                '3': '<span class="badge bg-success">Complete</span>'
            };

            $('#view_id').text(btn.attr('data-id'));
            $('#view_customer_name').text(btn.attr('data-customer-name'));
            $('#view_telephone').text(btn.attr('data-telephone') || 'N/A');
            $('#view_service').text(btn.attr('data-service-name') || 'N/A');
            $('#view_date').text(btn.attr('data-date') || 'N/A');
            $('#view_time').text(btn.attr('data-time') || 'N/A');
            $('#view_status').html(statusLabels[status] || '<span class="badge bg-secondary">N/A</span>');
            $('#view_note').text(btn.attr('data-note') || 'N/A');

        });

    });
    </script>

</body>

</html>