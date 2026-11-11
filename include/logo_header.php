<?php
include_once $base_path . 'include/dbconnection.php';

// Fetch branding data
$branding_query = mysqli_query($con, "SELECT company_name, company_logo FROM branding LIMIT 1");
$branding_data = mysqli_fetch_assoc($branding_query);

$company_name = $branding_data['company_name'] ?? 'Kaiadmin';
$logo_path = (isset($branding_data['company_logo']) && !empty($branding_data['company_logo']))
             ? $base_path . 'assets/img/branding/' . $branding_data['company_logo']
             : $base_path . 'assets/img/kaiadmin/logo_light.svg';
?>
<div class="logo-header" data-background-color="dark">
    <a href="<?php echo $base_path; ?>index.php" class="logo d-flex justify-content-center" style="width: 100%;">
        <img src="<?php echo $logo_path; ?>" alt="<?php echo htmlspecialchars($company_name); ?>" class="navbar-brand mx-auto d-block" height="50" style="max-height: 70px; width: auto; margin: 0 auto;" />
    </a>
    <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
            <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
            <i class="gg-menu-left"></i>
        </button>
    </div>
    <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
    </button>
</div>