<?php 
session_start(); // Start the session
$base_path = '../';
include '../include/dbconnection.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: auth/login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- header -->
    <?php include '../include/header.php';?>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include '../include/sidebar.php';?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <?php include '../include/logo_header.php';?>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <?php include '../include/topbar.php';?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Servicers Add</div>
                                </div>
                                <form action="servicers_submit.php" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <!--Service name-->
                                                <div class="form-group">
                                                    <label for="servicer_name">Servicer Name</label>
                                                    <input type="text" class="form-control" id="servicer_name"
                                                        name="servicer_name" placeholder="Enter Servicer Name"
                                                        required />
                                                </div>
                                                <!-- Service Duration -->
                                                <div class="form-group">
                                                    <label for="service_time">Service Duration (Minutes)</label>
                                                    <input type="number" class="form-control" id="service_time"
                                                        name="service_time"
                                                        placeholder="Enter duration in minutes (e.g. 30)" min="1"
                                                        step="1" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">

                                                <div class="form-group">
                                                    <label for="price">Price</label>
                                                    <input type="number" step="0.01" class="form-control" id="price"
                                                        name="price" placeholder="0.00" required />
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action">
                                        <button type="submit" name="save_product"
                                            class="btn btn-success">Submit</button>
                                        <button type="reset" class="btn btn-danger">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../include/footer.php';?>
        </div>


    </div>

    <!-- include/script.php -->
    <?php include "../include/script.php"; ?>

    <!--<script>
    $(document).ready(function() {
        $('#parent_category_id').change(function() {
            var parentId = $(this).val();
            if (parentId) {
                $.ajax({
                    type: 'POST',
                    url: 'get_sub_categories.php',
                    data: {
                        parent_id: parentId
                    },
                    success: function(html) {
                        $('#sub_category_id').html(html);
                    }
                });
            } else {
                $('#sub_category_id').html('<option value="">Select Sub Category</option>');
            }
        });
    });
    </script>-->

</body>

</html>