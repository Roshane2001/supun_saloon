  <?php 
  include_once __DIR__ . '/dbconnecting.php';
$base_path = $base_path ?? '../../';
$branding_query = mysqli_query($con, "SELECT company_name, company_favicon FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin - Bootstrap 5 Admin Dashboard';
$favicon_path = (isset($branding_data['company_favicon']) && !empty($branding_data['company_favicon']))
                ? $base_path . 'assets/img/branding/' . $branding_data['company_favicon']
                : $base_path . 'assets/img/kaiadmin/favicon.ico';
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($company_name); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link rel ="icon" type="image/x-icon" href="<?php echo htmlspecialchars($favicon_path); ?>">
  <link rel="stylesheet" href="assets/styles.css">
  <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
      rel="stylesheet">