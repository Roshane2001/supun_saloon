<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'include/head.php'; ?>

    <style>

    </style>
    <style id="__doc-stream-fit">
    html,
    body {
        margin: 0 !important;
        background: transparent !important
    }

    body>* {
        box-shadow: none !important
    }

    html,
    body {
        padding: 0 !important
    }

    body>* {
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
        box-sizing: border-box !important;
        border: none !important;
        border-radius: 0 !important
    }
    </style>
    <style id="tnet-doc-img-css" data-doc-runtime="">
    @keyframes tnetImgSpin {
        to {
            transform: rotate(360deg);
        }
    }

    .tnet-img-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #f0f0f0;
        border-radius: 8px;
        color: #9E9E9E;
        font: 13px 'DM Sans', -apple-system, sans-serif;
        box-sizing: border-box;
    }

    .tnet-img-spinner {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid rgba(26, 115, 232, 0.2);
        border-top-color: #1a73e8;
        animation: tnetImgSpin 0.8s linear infinite;
    }
    </style>
</head>

<body>

    <!-- Sticky Navigation Header -->
    <?php include 'include/header.php'; ?>

    <!-- Main Content Wrapper -->
    <main>
        <!-- Hero Section -->
        <?php include 'sections/hero.php'; ?>

        <!-- Hero Section -->
        <?php include 'sections/booking/booking.php'; ?>


        <!-- Services Section -->
        <?php include 'sections/service.php'; ?>

        <!-- About Section -->
        <!--<?php include 'sections/about.php'; ?>-->

        <!-- Pricing Menu Section -->
        <!--<?php include 'sections/pricing_section.php'; ?>-->

        <!-- Stylists Section -->
        <!--<?php include 'sections/stylist_section.php'; ?>-->

        <!-- Reviews Section -->
        <?php include 'sections/review.php'; ?>

        <!-- Booking Form Section -->
        
    </main>

    <!-- Footer -->
    <?php include 'include/footer.php'; ?>

    <!-- Runtime Interactivity Script -->
    <img src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2032%2032%22%20fill%3D%22none%22%3E%3Cg%20opacity%3D%220.5%22%3E%3Cpath%20d%3D%22M23.1359%200H8.86796C3.9714%200%200.00195312%203.97033%200.00195312%208.86798V23.1391C0.00195312%2028.0368%203.9714%2032.0071%208.86796%2032.0071H23.1359C28.0325%2032.0071%2032.002%2028.0368%2032.002%2023.1391V8.86798C32.002%203.97033%2028.0325%200%2023.1359%200Z%22%20fill%3D%22%231f2124%22%2F%3E%3Cpath%20d%3D%22M13.3778%2011.0853H7.95898V6.75781H24.0644V11.0885H18.6127V25.3563H13.3778V11.0853Z%22%20fill%3D%22%23ffffff%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E"
        alt="Template.net" width="32" height="32"
        style="position:fixed;right:8px;bottom:8px;z-index:2147483647;pointer-events:none">
</body>

</html>