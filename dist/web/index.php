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

        <!-- About Section -->
        <?php include 'sections/about.php'; ?>


        <!-- Services Section -->
        <?php include 'sections/service.php'; ?>

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

    
</body>

</html>