<?php
session_start();
$base_path = './';

// Check login
if (!isset($_SESSION['staff_id'])) {
    header("Location: dist/web/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'include/header.php'; ?>
</head>

<body>
    <div class="wrapper">

        <!-- Sidebar -->
        <?php include 'include/sidebar.php'; ?>

        <div class="main-panel">

            <!-- Header -->
            <div class="main-header">
                <div class="main-header-logo">
                    <?php include 'include/logo_header.php'; ?>
                </div>

                <?php include 'include/topbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">

                    <!-- Dashboard Title -->
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                        </div>
                    </div>

                    <?php

                    // ====================================
                    // DASHBOARD COUNTS
                    // ====================================

                    // Active Customers
                    $customer_res = mysqli_query($con, "
                        SELECT COUNT(*) AS total
                        FROM tblcustomer
                        WHERE is_active = 1
                    ");

                    $customer_count = mysqli_fetch_assoc($customer_res)['total'] ?? 0;


                    // Total Sales
                    $sales_res = mysqli_query($con, "
                        SELECT SUM(grand_total) AS total_sales
                        FROM tblsale
                        WHERE sale_status = 'Completed'
                    ");

                    $total_sales = mysqli_fetch_assoc($sales_res)['total_sales'] ?? 0;


                    // Today's Sales
                    $today_sales_res = mysqli_query($con, "
                        SELECT SUM(grand_total) AS today_total
                        FROM tblsale
                        WHERE sale_status = 'Completed'
                        AND DATE(sale_date) = CURDATE()
                    ");

                    $today_sales = mysqli_fetch_assoc($today_sales_res)['today_total'] ?? 0;


                    // Active Staff
                    $staff_res = mysqli_query($con, "
                        SELECT COUNT(*) AS total
                        FROM tblstaff
                        WHERE is_active = 1
                    ");

                    $staff_count = mysqli_fetch_assoc($staff_res)['total'] ?? 0;


                    // Active Products
                    $product_res = mysqli_query($con, "
                        SELECT COUNT(*) AS total
                        FROM tblproduct
                        WHERE is_active = 1
                    ");

                    $product_count = mysqli_fetch_assoc($product_res)['total'] ?? 0;


                    // Total Cost
                    $cost_res = mysqli_query($con, "
                        SELECT SUM(si.quantity * si.purchase_price_at_sale) AS total_cost
                        FROM tblsale_items si
                        JOIN tblsale s ON si.sale_id = s.id
                        WHERE s.sale_status = 'Completed'
                    ");

                    $total_cost = mysqli_fetch_assoc($cost_res)['total_cost'] ?? 0;

                    $net_profit = $total_sales - $total_cost;


                    // Today's Cost
                    $today_cost_res = mysqli_query($con, "
                        SELECT SUM(si.quantity * si.purchase_price_at_sale) AS today_cost
                        FROM tblsale_items si
                        JOIN tblsale s ON si.sale_id = s.id
                        WHERE s.sale_status = 'Completed'
                        AND DATE(s.sale_date) = CURDATE()
                    ");

                    $today_cost = mysqli_fetch_assoc($today_cost_res)['today_cost'] ?? 0;

                    $today_net_profit = $today_sales - $today_cost;


                    // Active Suppliers
                    $supplier_res = mysqli_query($con, "
                        SELECT COUNT(*) AS total
                        FROM tblsuplier
                        WHERE is_active = 1
                    ");

                    $supplier_count = mysqli_fetch_assoc($supplier_res)['total'] ?? 0;


                    // ====================================
                    // ACTIVE APPOINTMENTS ONLY
                    // ====================================

                    $appointment_query = mysqli_query($con, "
                        SELECT tblappointment.*,
                               servicers.servicer_name AS service_name
                        FROM tblappointment
                        LEFT JOIN servicers
                            ON tblappointment.service_id = servicers.id
                        WHERE tblappointment.status = 1
                        ORDER BY tblappointment.id DESC
                    ");

                    if (!$appointment_query) {
                        die("Appointment Query Error: " . mysqli_error($con));
                    }

                    $appointment_count = mysqli_num_rows($appointment_query);

                    ?>

                    <!-- ==================================== -->
                    <!-- DASHBOARD CARDS -->
                    <!-- ==================================== -->

                    <div class="row">

                        <!-- Day Sales -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-luggage-cart"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Day Sales</p>
                                                <h4 class="card-title">
                                                    LKR <?php echo number_format($today_sales, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Day Net Profit -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Day Net Profit</p>
                                                <h4 class="card-title">
                                                    LKR <?php echo number_format($today_net_profit, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customers -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Customers</p>
                                                <h4 class="card-title">
                                                    <?php echo number_format($customer_count); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Staff -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-user-check"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Staff</p>
                                                <h4 class="card-title">
                                                    <?php echo number_format($staff_count); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Sales -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Sales</p>
                                                <h4 class="card-title">
                                                    LKR <?php echo number_format($total_sales, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Net Profit -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-chart-pie"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Net Profit</p>
                                                <h4 class="card-title">
                                                    LKR <?php echo number_format($net_profit, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="far fa-check-circle"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Products</p>
                                                <h4 class="card-title">
                                                    <?php echo number_format($product_count); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-truck"></i>
                                            </div>
                                        </div>

                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Supplier Count</p>
                                                <h4 class="card-title">
                                                    <?php echo number_format($supplier_count); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- ==================================== -->
                    <!-- ACTIVE APPOINTMENTS TABLE -->
                    <!-- ==================================== -->

                    <div class="row mt-3">
                        <div class="col-md-12">

                            <div class="card card-round">

                                <div class="card-header">
                                    <div class="card-head-row">

                                        <div class="card-title">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            Active Appointments
                                        </div>

                                        <div class="card-tools">
                                            <span class="badge badge-success">
                                                <?php echo $appointment_count; ?> Appointments
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="table-responsive">

                                        <table class="table table-hover table-striped">

                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Customer Name</th>
                                                    <th>Telephone Number</th>
                                                    <th>Service Name</th>
                                                    <th>Date & Time</th>
                                                    <th>Note</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                <?php

                                                if ($appointment_count > 0) {

                                                    while ($appointment = mysqli_fetch_assoc($appointment_query)) {

                                                ?>

                                                    <tr>

                                                        <td>
                                                            <?php echo htmlspecialchars($appointment['id'] ?? ''); ?>
                                                        </td>

                                                        <td>
                                                            <strong>
                                                                <?php echo htmlspecialchars($appointment['customer_name'] ?? ''); ?>
                                                            </strong>
                                                        </td>

                                                        <td>
                                                            <?php echo htmlspecialchars($appointment['telephone_number'] ?? 'N/A'); ?>
                                                        </td>

                                                        <td>
                                                            <span class="badge badge-info">
                                                                <?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?>
                                                            </span>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            $date_part = !empty($appointment['date'])
                                                                ? date('d M Y', strtotime($appointment['date']))
                                                                : 'N/A';
                                                            $time_part = !empty($appointment['time'])
                                                                ? htmlspecialchars($appointment['time'])
                                                                : '';
                                                            echo $date_part;
                                                            if ($time_part !== '') {
                                                                echo ' <span class="text-muted">' . $time_part . '</span>';
                                                            }
                                                            ?>
                                                        </td>

                                                        <td>
                                                            <?php echo htmlspecialchars($appointment['note'] ?? '-'); ?>
                                                        </td>

                                                    </tr>

                                                <?php
                                                    }

                                                } else {
                                                ?>

                                                    <tr>
                                                        <td colspan="7" class="text-center py-4">
                                                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>

                                                            <p class="text-muted mb-0">
                                                                No active appointments found.
                                                            </p>
                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- END ACTIVE APPOINTMENTS -->

                </div>
            </div>

            <!-- Footer -->
            <?php include 'include/footer.php'; ?>

        </div>
    </div>

    <!-- Scripts -->
    <?php include "include/script.php"; ?>

</body>
</html>
