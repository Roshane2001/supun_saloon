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
$branding_query = mysqli_query($con, "SELECT company_name, company_logo FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin';
$logo_path = (isset($branding_data['company_logo']) && !empty($branding_data['company_logo']))
             ? $base_path . 'assets/img/branding/' . $branding_data['company_logo']
             : $base_path . 'assets/img/kaiadmin/logo_light.svg';
?>
<header class="header" data-wsb-header="1">
    <div class="container">
      <nav class="navbar">
        <a href="#hero" class="brand-logo-link">
          <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Aura Luxe Salon logo" data-email-logo="1" style="height:36px;width:auto;display:block">
          <span class="brand-title"><?php echo htmlspecialchars($company_name); ?></span>
        </a>

        <button class="nav-toggle-btn" data-wsb-toggle="nav" aria-label="Toggle navigation">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <ul class="nav-links" data-wsb-nav="">
          <li><a href="#services" class="nav-link">Services</a></li>
          <!--<li><a href="#about" class="nav-link">About Us</a></li>-->
          <!--<li><a href="#stylists" class="nav-link">Stylists</a></li>-->
          <li><a href="#reviews" class="nav-link">Reviews</a></li>
          <li><a href="#booking" class="btn-custom btn-primary-custom" style="padding: 10px 20px; font-size: 0.8rem;">Book Appointment</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <script>
  (function () {
    var toggleBtn = document.querySelector('[data-wsb-toggle="nav"]');
    var navMenu = document.querySelector('[data-wsb-nav]');

    if (!toggleBtn || !navMenu) {
      return;
    }

    function setNav(open) {
      if (open) {
        navMenu.setAttribute('data-wsb-open', '1');
        toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        navMenu.removeAttribute('data-wsb-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    }

    setNav(false);

    toggleBtn.addEventListener('click', function () {
      setNav(navMenu.getAttribute('data-wsb-open') !== '1');
    });

    navMenu.addEventListener('click', function (event) {
      if (event.target.closest('a')) {
        setNav(false);
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        setNav(false);
      }
    });
  })();
  </script>