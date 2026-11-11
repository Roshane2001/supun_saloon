<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <?php include $base_path . 'include/logo_header.php'; ?>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="dashboard">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>index.php">
                                    <span class="sub-item">Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--Staff List-->
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#staff" class="collapsed" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <p>Staff</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="staff">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>staff/staff_form.php">
                                    <span class="sub-item">Staff Add</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>staff/staff_list.php">
                                    <span class="sub-item">Staff List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--Category List-->
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#category" class="collapsed" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <p>Category</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="category">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>category/category_add.php">
                                    <span class="sub-item">Category Add</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>category/category_list.php">
                                    <span class="sub-item">Category List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <!--Reports-->
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#reports" class="collapsed" aria-expanded="false">
                        <i class="fas fa-chart-bar"></i>
                        <p>Reports</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="reports">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>reports/sales_report.php">
                                    <span class="sub-item">Sales Report</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>reports/monthly_sales_report.php">
                                    <span class="sub-item">Monthly Sales Report</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>reports/print_report.php">
                                    <span class="sub-item">Report Print</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <!--Product List-->
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#product" class="collapsed" aria-expanded="false">
                        <i class="fas fa-box"></i>
                        <p>Product</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="product">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>product/product_add.php">
                                    <span class="sub-item">Product Add</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>product/product_list.php">
                                    <span class="sub-item">Product List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <!--Suplier List-->
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#suplier" class="collapsed" aria-expanded="false">
                        <i class="fas fa-truck"></i>
                        <p>supplier</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="suplier">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>suplier/suplier_add.php">
                                    <span class="sub-item">Supplier Add</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>suplier/suplier_list.php">
                                    <span class="sub-item">Supplier List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <!--Services List-->
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#servicer" class="collapsed" aria-expanded="false">
                        <i class="fas fa-user-tie"></i>
                        <p>Services</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="servicer">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>servicers/servicers_add.php">
                                    <span class="sub-item">Add Service</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>servicers/servicers_list.php">
                                    <span class="sub-item">Service List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!--Appoinment List-->
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#appointment" class="collapsed" aria-expanded="false">
                        <i class="fas fa-user-tie"></i>
                        <p>Appointments</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="appointment">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>appointment/appointment_list.php">
                                    <span class="sub-item">Appointment List</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>appointment/appointment_add.php">
                                    <span class="sub-item">Appointment Add</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!--POS Cart-->
                
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#pos" class="collapsed" aria-expanded="false">
                        <i class="fas fa-money-bill"></i>
                        <p>POS Cart</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="pos">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>cart/pos.php">
                                    <span class="sub-item">POS</span>
                                </a>
                            </li>                           
                        </ul>
                    </div>
                </li>-->

                <!--Stock Management-->
                <!--<li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#stock">
                        <i class="fas fa-archive"></i>
                        <p>Stock</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="stock">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>stock/stock_dashboard.php">
                                    <span class="sub-item">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>stock/stock_list.php">
                                    <span class="sub-item">Current Stock</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo $base_path; ?>stock/stock_history.php">
                                    <span class="sub-item">Stock History</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <!--User Activity-->
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#user-activity">
                        <i class="fas fa-history"></i>
                        <p>User Activity</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="user-activity">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>user_activity/user_activity.php">
                                    <span class="sub-item">User Activity</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!--Branding-->
                <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#branding">
                        <i class="fas fa-cog"></i>
                        <p>Branding</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="branding">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="<?php echo $base_path; ?>branding/branding.php">
                                    <span class="sub-item">Branding</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>                                                                                                                                                                          
            </ul>
        </div>
    </div>
</div>