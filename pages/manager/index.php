<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Material Dash</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <!-- Layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>
    <div class="body-wrapper">
        <!-- Sidebar -->
        <?php include('pages/template/sidebar_owner.php') ?>
        <!-- End Sidebar -->

        <div class="main-wrapper mdc-drawer-app-content">
            <!-- Navbar -->
            <?php include('pages/template/navbar.php') ?>
            <!-- End Navbar -->
            <!-- Content -->
            <?php include('pages/controller/routes_owner.php') ?>
            <!-- End Content -->
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- End inject -->

    <!-- Plugin js for this page -->
    <script src="assets/vendors/chartjs/Chart.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- End plugin js for this page -->

    <!-- Custom js for this page -->
    <script src="assets/js/material.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->

</body>

</html>