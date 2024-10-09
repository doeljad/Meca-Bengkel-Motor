<?php
include('../../controller/connection.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$id_user = $_SESSION['id_user'];
$id_produk = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];
$catatan = $_POST['catatan'];

// Get customer ID
$query_pelanggan = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE id_user = ?");
$query_pelanggan->bind_param('i', $id_user);
$query_pelanggan->execute();
$result_pelanggan = $query_pelanggan->get_result();

if ($result_pelanggan->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User data not found.']);
    exit;
}

$id_pelanggan = $result_pelanggan->fetch_assoc()['id_pelanggan'];

// Insert into keranjang
// Cek apakah sudah ada keranjang aktif untuk pelanggan tersebut
$query_check = $conn->prepare("SELECT id_keranjang FROM keranjang WHERE id_pelanggan = ? AND status = 'aktif'");
$query_check->bind_param('i', $id_pelanggan);
$query_check->execute();
$result_check = $query_check->get_result();

if ($result_check->num_rows > 0) {
    // Jika ada keranjang aktif, ambil id_keranjang yang sudah ada
    $row = $result_check->fetch_assoc();
    $id_keranjang = $row['id_keranjang'];
} else {
    // Jika tidak ada keranjang aktif, lakukan INSERT untuk membuat keranjang baru
    $query_keranjang = $conn->prepare("INSERT INTO keranjang (id_pelanggan, status) VALUES (?, 'aktif')");
    $query_keranjang->bind_param('i', $id_pelanggan);
    $query_keranjang->execute();
    $id_keranjang = $conn->insert_id;
}

// Insert into detail_keranjang
$query_detail = $conn->prepare("INSERT INTO detail_keranjang (id_keranjang, id_produk, jumlah, catatan) VALUES (?, ?, ?, ?)");
$query_detail->bind_param('iiss', $id_keranjang, $id_produk, $jumlah, $catatan);
$query_detail->execute();
$id_detail_keranjang = $conn->insert_id;

// Update stock in the product table
$query_stock = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
$query_stock->bind_param('ii', $jumlah, $id_produk);
$query_stock->execute();

echo json_encode(['status' => 'success', 'message' => 'Product added to cart successfully.']);
