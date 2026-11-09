<?php
session_start();
$base_path = '../'; // Corrected path to point to the root from the 'auth' directory
include $base_path . 'include/dbconnecting.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['customer_id'])) {
    header("Location: ../booking.php"); // Corrected path to the main dashboard
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Using customername as username and nic_no as password based on provided SQL
        $query = "SELECT s.*, r.role 
                  FROM tblcustomer s 
                  JOIN tblrole r ON s.role_id = r.id 
                  WHERE s.customername = '$username' AND s.is_active = 1 
                  LIMIT 1";
        
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['customer_id'] = $user['id'];
                $_SESSION['customer_name'] = $user['customer_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Log successful login activity
                $customer_id = $user['id'];
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
                $log_query = "INSERT INTO tbluser_activity (customer_id, action, ip_address, user_agent) 
                              VALUES ('$customer_id', 'Login Success', '$ip_address', '$user_agent')";
                mysqli_query($con, $log_query);

                header("Location: ../web/index.php"); // Corrected path to the main dashboard
                exit();
            } else {
                $error = 'Invalid credentials or account inactive.';
            }
        } else {
            $error = 'Invalid credentials or account inactive.';
            // Log failed login attempt
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
            $log_query = "INSERT INTO tbluser_activity (action, ip_address, user_agent, details) 
                          VALUES ('Login Failed', '$ip_address', '$user_agent', 'Attempted Username: $username')";
            mysqli_query($con, $log_query);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../include/head.php'; ?>
    <title>Customer Login - <?php echo $company_name; ?></title>
</head>

<body>

    <!-- Booking Form Section -->
    <section id="booking" class="section-padding booking-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <form class="booking-form" data-wsb-submit="message"
                        data-wsb-success-text="Thank you! Your appointment request has been submitted. We will contact you shortly to confirm.">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="fullname">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" class="form-control"
                                        placeholder="e.g. Sarah Jenkins" required="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder="sarah@example.com" required="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control"
                                        placeholder="(555) 019-2834" required="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="preferred-date">Preferred Date</label>
                                    <input type="date" id="preferred-date" name="preferred_date" class="form-control"
                                        required="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="preferred-time">Preferred Time</label>
                                    <input type="time" id="preferred-time" name="preferred_time" class="form-control"
                                        required="">
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
</body>

</html>