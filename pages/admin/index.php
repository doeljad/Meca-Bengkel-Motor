<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Percetakan Bima</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <!-- Layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Zb4Bsg-fpAM4ytjm"></script>
    <style>
        /* Tombol Gradient dan Rounded */
        .btn {
            display: inline-block;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            color: #fff;
            border: none;
            border-radius: 25px;
            /* Membuat sudut rounded */
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }

        /* Gradient untuk tombol Success */
        .btn-success {
            background: linear-gradient(135deg, #28a745, #34d058, #22863a);
        }

        /* Gradient untuk tombol Danger */
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #f66d6d, #c82333);
        }

        /* Gradient untuk tombol Info */
        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #1ab6d9, #138496);
        }

        .btn:hover {
            transform: scale(1.05);
            /* Efek zoom saat hover */
            opacity: 0.9;
            /* Menambahkan efek transparansi saat hover */
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3, #00408d);
        }

        .btn:focus,
        .btn:active {
            outline: none;
            /* Menghapus outline default */
            box-shadow: none;
            /* Menghapus shadow default */
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- Sidebar -->
        <!-- End Sidebar -->
        <?php include('pages/template/navbar.php') ?>

        <div class="container-fluid page-body-wrapper">
            <?php include('pages/template/sidebar_admin.php') ?>
            <!-- Navbar -->
            <!-- End Navbar -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="assets/vendors/js/vendor.bundle.base.js"></script>
            <!-- Content -->
            <?php include('pages/controller/routes_admin.php') ?>
            <!-- End Content -->
        </div>
    </div>

    <!-- Scripts -->

    <script src="assets/vendors/chartjs/Chart.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/material.js"></script>
    <script src="assets/js/misc.js"></script>

    <script>
        $(document).ready(function() {
            $('#editProfileModal').on('show.bs.modal', function(e) {
                var userId = $('#user_id').val();
                $.ajax({
                    url: 'pages/admin/controller/get_users.php',
                    type: 'POST',
                    data: {
                        id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response) {
                            $('#name').val(response.name);
                            $('#username').val(response.username);
                            $('#user_id').val(response.id_user);
                        } else {
                            alert('User data not found');
                        }
                    },
                    error: function() {
                        alert('Error retrieving user data');
                    }
                });
            });
        });
    </script>
</body>

</html>