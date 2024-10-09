<?php
// Periksa apakah URL hanya "/index.php" tanpa parameter
if (!isset($_GET['page'])) {
    // Redirect ke "/index.php?page=home"
    echo "<script>window.location.href = 'index.php?page=home'</script>";
    exit(); // Hentikan eksekusi skrip setelah melakukan redirect
}
// Ambil parameter dari URL
$params = isset($_GET['page']) ? $_GET['page'] : 'home';

// Definisikan route
$routes = [
    // Login 
    'login' => 'login.php',

    // Admin
    'home' => 'pages/customer/home.php',
    'detail-barang' => 'pages/customer/detail-barang.php',
    'keranjang' => 'pages/customer/keranjang.php',
    'kategori' => 'pages/customer/kategori.php',
    'pesanan' => 'pages/customer/pesanan.php',
    'setting' => 'pages/customer/setting.php',
    'chat' => 'pages/customer/chat.php',
    'chat-detail' => 'pages/customer/chat-detail.php',
    'chat-kirim-pesan' => 'pages/customer/chat-kirim-pesan.php',
    'chat-mulai-obrolan' => 'pages/customer/chat-mulai-obrolan.php',

];

// Periksa apakah URL ada di route
if (isset($routes[$params])) {
    // Tentukan halaman yang akan dimuat
    $page = $routes[$params];
    // Include halaman yang sesuai
    include_once($page);
} else {
    // Jika URL tidak ada di route, redirect ke halaman 404 atau halaman lain yang sesuai
    echo "<script>window.location.href = 'pages/template/error-404.html'</script>";
    exit(); // Penting untuk menghentikan eksekusi skrip setelah melakukan redirect
}
