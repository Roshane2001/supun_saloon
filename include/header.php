<?php
include_once $base_path . 'include/dbconnection.php';

// Fetch branding data
$branding_query = mysqli_query($con, "SELECT company_name, company_favicon FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin - Bootstrap 5 Admin Dashboard';
$favicon_path = (isset($branding_data['company_favicon']) && !empty($branding_data['company_favicon']))
                ? $base_path . 'assets/img/branding/' . $branding_data['company_favicon']
                : $base_path . 'assets/img/kaiadmin/favicon.ico';
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?php echo htmlspecialchars($company_name); ?></title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="<?php echo $favicon_path; ?>"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="<?php echo $base_path; ?>assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["<?php echo $base_path; ?>assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/plugins.min.css" />
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/demo.css" />

<!-- Show/Hide Password -->
<style>
    .position-relative { position: relative; }
    .form-floating-label { position: relative; }
    .position-relative .form-control { padding-right: 35px; }
    .show-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 10;
        color: #6c757d;
        font-size: 16px;
    }
    .login .show-password {
        right: 20px;
        font-size: 20px;
    }
    #password { padding-right: 40px; }
</style>