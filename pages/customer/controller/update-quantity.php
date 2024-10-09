<?php
include('../../controller/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_detail_keranjang = $_POST['id_detail_keranjang'];
    $action = $_POST['action'];

    // Mengambil jumlah saat ini
    $sql = "SELECT jumlah FROM detail_keranjang WHERE id_detail_keranjang = $id_detail_keranjang";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $jumlah = $row['jumlah'];

    // Mengupdate jumlah berdasarkan aksi
    if ($action == 'plus') {
        $jumlah++;
    } elseif ($action == 'minus' && $jumlah > 1) {
        $jumlah--;
    }

    // Update ke database
    $sql_update = "UPDATE detail_keranjang SET jumlah = $jumlah WHERE id_detail_keranjang = $id_detail_keranjang";
    $conn->query($sql_update);

    echo json_encode(['status' => 'success']);
}
