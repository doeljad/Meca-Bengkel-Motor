<?php
include('pages/controller/connection.php');

// Perform the following queries only if id_user is set
$query = "
SELECT k.id_kategori, k.nama_kategori
FROM kategori k";
$kategori = $conn->query($query);


if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];


    // Get id_pelanggan for the logged-in user
    $query_pelanggan = "SELECT id_pelanggan FROM pelanggan WHERE id_user = ?";
    $stmt_pelanggan = $conn->prepare($query_pelanggan);
    $stmt_pelanggan->bind_param('i', $id_user);
    $stmt_pelanggan->execute();
    $result_pelanggan = $stmt_pelanggan->get_result();

    if ($result_pelanggan->num_rows === 0) {
        // Handle case where user data is not found
        die('User data not found');
    }

    $pelanggan = $result_pelanggan->fetch_assoc();
    $id_pelanggan = $pelanggan['id_pelanggan'];

    // Get the total count of items in the active cart
    $query_cart_count = "
       SELECT COUNT(*) as total_items
FROM detail_keranjang
JOIN keranjang ON detail_keranjang.id_keranjang = keranjang.id_keranjang
WHERE keranjang.id_pelanggan = ? AND keranjang.status = 'aktif';
    ";
    $stmt_cart_count = $conn->prepare($query_cart_count);
    $stmt_cart_count->bind_param('i', $id_pelanggan);
    $stmt_cart_count->execute();
    $result_cart_count = $stmt_cart_count->get_result();
    $total_items = $result_cart_count->fetch_assoc()['total_items'] ?? 0;
} else {
    // Do nothing if id_user is not set
    $total_items = 0; // Default cart count to 0 if user is not logged in
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Percetakan Bima</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet"> -->

    <!-- Libraries Stylesheet -->
    <!-- <link rel="stylesheet" href="assets/css/templatemo-hexashop.css"> -->
    <link href="assets/css/animate.min.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Zb4Bsg-fpAM4ytjm"></script>

</head>

<body>

    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg bg-white  py-3 py-lg-0 px-0 mt-4 mb-4">
                        <button class="navbar-toggler text-dark" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="collapse navbar-collapse justify-content-between " id="navbarCollapse">
                            <div class="navbar-nav mr-auto py-0 ">
                                <a href="?page=home" class="nav-item nav-link active ">Home</a>
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link " data-toggle="dropdown">Kategori </a>
                                    <div class="dropdown-menu rounded-0 border-0 m-0 ">
                                        <?php while ($row = $kategori->fetch_assoc()) : ?>
                                            <a href="?page=kategori&id=<?php echo $row['id_kategori']; ?>" class="dropdown-item"><?php echo htmlspecialchars($row['nama_kategori']); ?></a>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="navbar-nav ml-auto py-0 d-flex align-items-center ">
                                <a href="?page=keranjang" class="btn px-0 ml-3">
                                    <i class="fas fa-shopping-cart text-dark"></i>
                                    <span id="cart-count" class="badge text-dark border border-dark rounded-circle" style="padding-bottom: 2px;"><?php echo $total_items; ?></span>
                                </a>
                                <div class="nav-item dropdown ml-3">
                                    <?php if (isset($_SESSION['name'])) : ?>
                                        <a href="#" class="nav-link " data-toggle="dropdown"><?php echo htmlspecialchars($_SESSION['name']); ?> <i class="fa fa-angle-down mt-1"></i></a>
                                        <div class="dropdown-menu rounded-0 border-0 m-0">
                                            <a href="index.php?page=pesanan" class="dropdown-item">Pesanan Saya</a>
                                            <a href="?page=setting" class="dropdown-item">Pengaturan</a>
                                            <a href="logout.php" class="dropdown-item">Logout</a>
                                        </div>
                                    <?php else : ?>
                                        <a href="login.php" class="nav-link">Login</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>



    <!-- Navbar End -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="assets/js/easing.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/main.js"></script>

    <?php include("pages/controller/routes_customer.php") ?>


    <!-- Footer Start -->
    <footer>
        <div class="footer_section layout_padding">
            <div class="container">
                <div class="footer_logo"><a href="index.html"><img src="assets/images/logo-white.png" width="190"></a></div>

                <div class="footer_menu">
                    <ul>
                        <li><a href="index.php?page=pesanan">Pesanan Saya</a></li>
                        <li><a href="index.php?page=setting">Pengaturan</a></li>
                    </ul>
                </div>
                <div class="location_main">Help Line Number : <a href="https://wa.me/+6285233656947">085233656947</a></div>
            </div>
        </div>
        <!-- footer section end -->
        <!-- copyright section start -->
        <div class="copyright_section">
            <div class="container">
                <p class="copyright_text">© 2024 All Rights Reserved. Valentino Yoss Mahendra</p>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

</body>

</html>