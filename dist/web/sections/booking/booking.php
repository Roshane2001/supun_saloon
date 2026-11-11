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