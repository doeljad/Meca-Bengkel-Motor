<?php
include('../../controller/connection.php');
require_once dirname(__FILE__) . '../../../payment/midtrans/Midtrans.php';

session_start();

ini_set('display_errors', 1); // Tampilkan error
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan pelanggan telah login
if (!isset($_SESSION['id_user'])) {
    $response = ['status' => 'error', 'message' => 'User not logged in.'];
    echo json_encode($response);
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data pelanggan
$query_pelanggan = "SELECT id_pelanggan, nama, email, no_telp FROM pelanggan WHERE id_user = ?";
$stmt_pelanggan = $conn->prepare($query_pelanggan);
$stmt_pelanggan->bind_param('i', $id_user);
$stmt_pelanggan->execute();
$result_pelanggan = $stmt_pelanggan->get_result();

if ($result_pelanggan->num_rows === 0) {
    $response = ['status' => 'error', 'message' => 'User data not found.'];
    echo json_encode($response);
    exit;
}

$pelanggan = $result_pelanggan->fetch_assoc();
$id_pelanggan = $pelanggan['id_pelanggan'];

// Ambil data dari POST
$input = json_decode(file_get_contents('php://input'), true);
$amount = intval($input['amount']); // Jumlah total dari frontend

// Ambil ID Keranjang Aktif
$query_keranjang = $conn->prepare("SELECT id_keranjang FROM keranjang WHERE id_pelanggan = ? AND status = 'aktif'");
$query_keranjang->bind_param('i', $id_pelanggan);
$query_keranjang->execute();
$result_keranjang = $query_keranjang->get_result();

if ($result_keranjang->num_rows === 0) {
    $response = ['status' => 'error', 'message' => 'No active cart found for the user.'];
    echo json_encode($response);
    exit;
}

$id_keranjang = $result_keranjang->fetch_assoc()['id_keranjang'];

// Buat ID Pesanan
$id_pesanan = bin2hex(random_bytes(16));

// Buat entri baru di tabel pesanan
$query_pesanan = $conn->prepare("INSERT INTO pesanan (id_pesanan, id_pelanggan, tanggal_pesanan, total, status) VALUES (?, ?, NOW(), ?, '0')");
$query_pesanan->bind_param('sii', $id_pesanan, $id_pelanggan, $amount);

if (!$query_pesanan->execute()) {
    $response = ['status' => 'error', 'message' => 'Failed to create order.'];
    echo json_encode($response);
    exit;
}

// Pindahkan data dari detail_keranjang ke detail_pesanan
$query_detail_keranjang = $conn->prepare("SELECT * FROM detail_keranjang WHERE id_keranjang = ?");
$query_detail_keranjang->bind_param('i', $id_keranjang);
$query_detail_keranjang->execute();
$result_detail_keranjang = $query_detail_keranjang->get_result();

while ($row = $result_detail_keranjang->fetch_assoc()) {
    $id_produk = $row['id_produk'];
    $jumlah = $row['jumlah'];
    $catatan = $row['catatan'];

    $query_detail_pesanan = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, catatan) VALUES (?, ?, ?, ?)");
    $query_detail_pesanan->bind_param('siss', $id_pesanan, $id_produk, $jumlah, $catatan);

    if (!$query_detail_pesanan->execute()) {
        $response = ['status' => 'error', 'message' => 'Failed to move cart details to order.'];
        echo json_encode($response);
        exit;
    }
}

// Ubah status keranjang menjadi 'selesai'
$query_update_keranjang = $conn->prepare("UPDATE keranjang SET status = 'selesai' WHERE id_keranjang = ?");
$query_update_keranjang->bind_param('i', $id_keranjang);
$query_update_keranjang->execute();

// Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-hDP53tQPqBYDEaMpygPrI75T';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Buat parameter transaksi
$params = [
    'transaction_details' => [
        'order_id' => $id_pesanan,
        'gross_amount' => $amount,
    ],
    'customer_details' => [
        'first_name' => $pelanggan['nama'],
        'email' => $pelanggan['email'],
        'phone' => $pelanggan['no_telp'],
    ],
];

try {
    // Buat Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    $response = ['status' => 'success', 'token' => $snapToken];
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
