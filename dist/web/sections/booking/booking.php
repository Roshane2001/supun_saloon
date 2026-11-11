<?php 
$base_path = '../../';
include __DIR__ . '/../../include/dbconnecting.php';

// Fetch branding data
$branding_query = mysqli_query($con, "SELECT * FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query) ?? [];

// Fetch services data
$services_query = mysqli_query($con, "SELECT * FROM servicers");
$services_data = mysqli_fetch_all($services_query, MYSQLI_ASSOC);

//Check if the user is logged in, if not, redirect to login page
/*if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth_customer/login_customer.php");
    exit();
}*/




?>


<!-- Booking Form Section -->
<section id="booking" class="section-padding booking-section">
    <div class="container">
        <?php if (isset($_GET['booking']) && $_GET['booking'] === 'success'): ?>
            <div class="booking-success-alert" role="alert">
                <button type="button" class="booking-success-close" onclick="this.parentElement.remove();"
                    aria-label="Close">&times;</button>
                <strong>Booking received!</strong> Your salon session request has been submitted successfully.
                Our concierge team will review it and confirm within 2 hours.
                <?php if (isset($_GET['booking_id']) && $_GET['booking_id'] !== ''): ?>
                    <div style="margin-top: 8px; font-weight: 600;">
                        Booking ID: #<?php echo htmlspecialchars($_GET['booking_id']); ?>
                    </div>
                <?php endif; ?>
            </div>
            <script>
                (function () {
                    if (window.history && window.history.replaceState) {
                        var url = new URL(window.location.href);
                        if (url.searchParams.has('booking')) {
                            url.searchParams.delete('booking');
                            url.searchParams.delete('booking_id');
                            window.history.replaceState({}, document.title, url.pathname + url.hash);
                        }
                    }
                })();
            </script>
        <?php endif; ?>

        <div class="row align-items-center">
            <div class="col-lg-6" style="margin-bottom: 30px;">
                <span class="sub-title">Online Reservations</span>
                <h2 class="main-title">Book Your Salon Session</h2>
                <p style="margin-top: 16px; margin-bottom: 24px;">Select your preferred date and service. Our concierge
                    team will review your booking request and confirm within 2 hours.</p>

                <!--<div style="margin-bottom: 20px;">
                    <h4 style="color: var(--bg-cream); font-size: 1.2rem; margin-bottom: 6px;">Opening Hours</h4>
                    <p style="font-size: 0.95rem;">Tuesday – Saturday: 9:00 AM – 7:00 PM<br>Sunday: 10:00 AM – 5:00
                        PM<br>Monday: Closed</p>
                </div>-->
                <div style="margin-bottom: 20px;">
                    <h4 style="color: var(--bg-cream); font-size: 1.2rem; margin-bottom: 6px;">Opening Hours</h4>
                    <p style="font-size: 0.95rem;">Seven Days Open</p>
                </div>

                <div>
                    <h4 style="color: var(--bg-cream); font-size: 1.2rem; margin-bottom: 6px;">Location</h4>
                    <p style="font-size: 0.95rem;"><?php echo htmlspecialchars($branding_data['address'] ?? ''); ?></p>
                </div>

                <div class="booking-map" style="margin-top: 18px; border-radius: var(--radius-md, 8px); overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.08);">
                    <iframe title="Salon location map"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.34911246596!2d79.99272241125047!3d7.200950792774523!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2e1a82b85d1e1%3A0x1bef35c591532203!2sEkala%20-%20Kotadeniyawa%20Rd!5e0!3m2!1sen!2slk!4v1794381761072!5m2!1sen!2slk"
                        width="100%" height="265" style="border: 0; display: block;"
                        loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen></iframe>
                </div>
            </div>

            <div class="col-lg-6">
                <form class="booking-form" action="sections/booking/booking_submit.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="fullname">Full Name</label>
                                <input type="text" id="fullname" name="customer_name" class="form-control"
                                    placeholder="Your Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Your Email (optional)">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="telephone_number">Phone Number</label>
                                <input type="tel" id="telephone_number" name="telephone_number" class="form-control"
                                    placeholder="07X XXXXXXX" inputmode="numeric" pattern="0[0-9]{9}"
                                    title="Enter a 10-digit number starting with 0, e.g. 0771234567"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="service">Select Service</label>
                                <select id="service" name="service_id" class="form-control" required>
                                    <option value="">Choose a service...</option>
                                    <?php foreach ($services_data as $service) { ?>
                                    <option value="<?php echo $service['id']; ?>">
                                        <?php echo htmlspecialchars($service['servicer_name']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="date">Preferred Date</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="preferred-time">Preferred Time</label>
                                <input type="time" id="preferred-time" name="preferred_time" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">Special Requests / Hair Goals</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control"
                            placeholder="Tell us about your current hair status or desired style..."></textarea>
                    </div>

                    <button type="submit" name="submit_booking" class="btn-custom btn-accent-custom"
                        style="width: 100%; margin-top: 10px;">Confirm Booking Request</button>
                </form>
            </div>
        </div>
    </div>
</section>