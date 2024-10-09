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
$id_detail_keranjang = $_POST['id_detail_keranjang'];

// Get product ID and quantity from detail_keranjang
$query_detail = $conn->prepare("SELECT id_produk, jumlah FROM detail_keranjang WHERE id_detail_keranjang = ?");
$query_detail->bind_param('i', $id_detail_keranjang);
$query_detail->execute();
$result_detail = $query_detail->get_result();

if ($result_detail->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cart item not found.']);
    exit;
}

$row_detail = $result_detail->fetch_assoc();
$id_produk = $row_detail['id_produk'];
$jumlah = $row_detail['jumlah'];

// Delete from detail_keranjang
$query_delete = $conn->prepare("DELETE FROM detail_keranjang WHERE id_detail_keranjang = ?");
$query_delete->bind_param('i', $id_detail_keranjang);
$query_delete->execute();

// Update stock in the product table
$query_stock = $conn->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
$query_stock->bind_param('ii', $jumlah, $id_produk);
$query_stock->execute();

echo json_encode(['status' => 'success', 'message' => 'Product removed from cart and stock updated.']);
