<?php
$base_path = $base_path ?? '../../';
include_once __DIR__ . '/dbconnecting.php';

// Fetch branding data
$branding_query = mysqli_query($con, "SELECT company_name, company_favicon FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin - Bootstrap 5 Admin Dashboard';
$favicon_path = (isset($branding_data['company_favicon']) && !empty($branding_data['company_favicon']))
                ? $base_path . 'assets/img/branding/' . $branding_data['company_favicon']
                : $base_path . 'assets/img/kaiadmin/favicon.ico';

// Fetch branding data
$branding_query = mysqli_query($con, "SELECT company_name, company_logo, telephone, company_email, address FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin';
$logo_path = (isset($branding_data['company_logo']) && !empty($branding_data['company_logo']))
             ? $base_path . 'assets/img/branding/' . $branding_data['company_logo']
             : $base_path . 'assets/img/kaiadmin/logo_light.svg';
?>
<footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-4" style="margin-bottom: 30px;">
          <div class="footer-brand">
            <img src="<?php echo $logo_path; ?>" alt="Aura Luxe Salon logo" data-email-logo="1" style="width:200px;display:block;margin-bottom:12px">
            <span class="footer-title"><?php echo $company_name; ?></span>
          </div>
          <p class="footer-desc">A premium hair and beauty studio committed to effortless luxury, bespoke styling, and hair health.</p>
        </div>

        <div class="col-md-4 col-lg-2" style="margin-bottom: 30px;">
          <h4 class="footer-heading">Navigation</h4>
          <ul class="footer-links">
            <li><a href="#hero">Home</a></li>
            <li><a href="#services">Services</a></li>
            <!--<li><a href="#about">About Us</a></li>-->
            <!--<li><a href="#pricing">Pricing</a></li>-->
            <li><a href="#stylists">Stylists</a></li>
          </ul>
        </div>

        <div class="col-md-4 col-lg-3" style="margin-bottom: 30px;">
          <h4 class="footer-heading">Services</h4>
          <ul class="footer-links">
            <li><a href="#services">Couture Cuts</a></li>
            <li><a href="#services">Balayage &amp; Color</a></li>
            <li><a href="#services">Keratin Smoothening</a></li>
            <li><a href="#services">Scalp Treatment</a></li>
            <li><a href="#services">Bridal Styling</a></li>
          </ul>
        </div>

        <div class="col-md-4 col-lg-3" style="margin-bottom: 30px;">
          <h4 class="footer-heading">Contact &amp; Hours</h4>
          <p style="font-size: 0.9rem; margin-bottom: 8px;">Telephone: <?php echo htmlspecialchars($branding_data['telephone'] ?? ''); ?></p>
          <p style="font-size: 0.9rem; margin-bottom: 8px;">Email: <?php echo htmlspecialchars($branding_data['company_email'] ?? ''); ?></p>
          <p style="font-size: 0.9rem;"> <?php echo htmlspecialchars($branding_data['address'] ?? ''); ?></p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>© 2026 Develope by @FEIT Solutions</p>
      </div>
    </div>
  </footer>