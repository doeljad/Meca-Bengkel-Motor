<?php
include('../../controller/connection.php');

$response = array();
header('Content-Type: application/json'); // Pastikan respons dalam format JSON

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int) $_GET['id']; // Casting untuk memastikan ID adalah integer

    // Query untuk mendapatkan stok produk berdasarkan ID
    $query = "SELECT stok FROM produk WHERE id_produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['stok'] = $row['stok']; // Mengirim stok sebagai respons
    } else {
        $response['success'] = false;
        $response['message'] = 'Produk tidak ditemukan.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'ID produk tidak disediakan atau tidak valid.';
}

echo json_encode($response);
