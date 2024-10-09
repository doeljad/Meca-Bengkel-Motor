<?php
include('../../controller/connection.php');
session_start();

// Pastikan pelanggan telah login
if (!isset($_SESSION['id_user'])) {
    $response = ['status' => 'error', 'message' => 'User not logged in.'];
    echo json_encode($response);
    exit;
}

$id_user = $_SESSION['id_user'];

// Mengambil data ongkir yang dikirim melalui request
$ongkir = isset($_POST['ongkir']) ? intval($_POST['ongkir']) : 0;

// Mengambil data keranjang
$query = "
   SELECT 
        produk.nama, 
        produk.harga, 
        detail_keranjang.jumlah 
    FROM 
        detail_keranjang 
    INNER JOIN 
        produk ON detail_keranjang.id_produk = produk.id_produk 
    INNER JOIN 
        keranjang ON detail_keranjang.id_keranjang = keranjang.id_keranjang 
    INNER JOIN 
        pelanggan ON pelanggan.id_pelanggan = keranjang.id_pelanggan
    INNER JOIN 
        users ON users.id_user = pelanggan.id_user 
    WHERE 
        users.id_user = ? AND keranjang.status = 'aktif'
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    $item_total = $row['harga'] * $row['jumlah'];
    $subtotal += $item_total;
    $items[] = [
        'nama' => $row['nama'],
        'harga' => $row['harga'],
        'jumlah' => $row['jumlah'],
        'total' => $item_total
    ];
}

$biaya_layanan = 2500;
$total = $subtotal + $biaya_layanan + $ongkir;

$response = [
    'status' => 'success',
    'subtotal' => $subtotal,
    'biaya_layanan' => $biaya_layanan,
    'ongkir' => $ongkir,
    'total' => $total,
    'items' => $items
];
echo json_encode($response);
exit;
