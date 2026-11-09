<?php
session_start();
$base_path = '../'; // Corrected path to point to the root from the 'auth' directory
include $base_path . 'include/dbconnection.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['staff_id'])) {
    header("Location: ../index.php"); // Corrected path to the main dashboard
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Using staff_name as username and nic_no as password based on provided SQL
        $query = "SELECT s.*, r.role 
                  FROM tblstaff s 
                  JOIN tblrole r ON s.role_id = r.id 
                  WHERE s.username = '$username' AND s.is_active = 1 
                  LIMIT 1";
        
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['staff_id'] = $user['id'];
                $_SESSION['staff_name'] = $user['staff_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Log successful login activity
                $user_id = $user['id'];
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $user_agent = mysqli_real_escape_string($con, $_SERVER['HTTP_USER_AGENT']);
                $log_query = "INSERT INTO tbluser_activity (user_id, action, ip_address, user_agent) 
                              VALUES ('$user_id', 'Login Success', '$ip_address', '$user_agent')";
                mysqli_query($con, $log_query);

                header("Location: ../index.php"); // Corrected path to the main dashboard
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
    <?php include '../include/header.php'; ?>
    <title>Login - POS System</title>
</head>

<body class="login">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <?php
            // Fetch branding data for the logo
            $branding_query = mysqli_query($con, "SELECT company_name, company_logo FROM branding LIMIT 1");
            $branding_data = mysqli_fetch_assoc($branding_query);

            $company_name = $branding_data['company_name'] ?? 'POS System';
            $logo_path = (isset($branding_data['company_logo']) && !empty($branding_data['company_logo']))
                         ? $base_path . 'assets/img/branding/' . $branding_data['company_logo']
                         : $base_path . 'assets/img/kaiadmin/logo_light.svg'; // Default logo if not set
            ?>
            <!--add company logo-->
            <div class="text-center">
                <img src="<?php echo $logo_path; ?>" alt="<?php echo htmlspecialchars($company_name); ?>"
                    class="navbar-brand" height="150" />
            </div>
            <h3 class="text-center">Sign In</h3>

            <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="login-form">
                    <div class="form-group form-floating-label">
                        <input id="username" name="username" type="text" class="form-control input-border-bottom"
                            required>
                        <label for="username" class="">Username (Staff Name)</label>
                    </div>
                    <div class="form-group form-floating-label">
                        <input id="password" name="password" type="password" class="form-control input-border-bottom"
                            required>
                        <label for="password" class="">Password</label>
                    </div>
                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-primary btn-rounded btn-login">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include $base_path . 'include/script.php'; ?>
    <script>
    // Handle floating labels for autofilled fields
    $('.form-control').each(function() {
        if ($(this).val() !== '') {
            $(this).addClass('filled');
        }
    });
    </script>
</body>

</html>