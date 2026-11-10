<!DOCTYPE html>
<html lang="en">

<head>
   

    <style>

    </style>
</head>
<body>

    <main>
<section id="booking-success" class="section-padding">
    <div class="container text-center">
        <div class="booking-success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h2>Your Booking Has Been Added!</h2>

            <p>
                <?php echo isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) . ', ' : ''; ?>we have successfully received your booking request.
                Our team will review your appointment and confirm it soon.
            </p>

            <a href="../index.php" class="booking-status">
                <i class="fas fa-clock"></i> Waiting for Confirmation
            </a>

            <!--<div class="mt-4">
                <a href="index.php" class="btn btn-primary">
                    Back to Home
                </a>
            </div>-->
        </div>
    </div>
</section>
</main>
</body>
</html>
<style>
.booking-success-card {
    max-width: 650px;
    margin: 0 auto;
    padding: 55px 30px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
}

.success-icon {
    font-size: 65px;
    color: #28a745;
    margin-bottom: 20px;
}

.booking-success-card h2 {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 15px;
}

.booking-success-card p {
    color: #777;
    font-size: 16px;
    line-height: 1.7;
}

.booking-status {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 18px;
    background: #fff3cd;
    color: #856404;
    border-radius: 30px;
    font-size: 14px;
}

.booking-success-card .btn {
    padding: 12px 30px;
    border-radius: 6px;
}
</style>