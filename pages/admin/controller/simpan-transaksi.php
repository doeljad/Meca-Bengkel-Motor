<?php
include('../../controller/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pesanan = $_POST['id_pesanan'];
    $tanggal_pembayaran = date('Y-m-d H:i:s');
    $total = $_POST['total'];
    $metode = $_POST['metode'];
    $status = $_POST['status']; // Status transaksi
    $statusPesanan = $_POST['statusPesanan']; // Status pesanan

    // Save transaction to database
    $sql_transaksi = "INSERT INTO transaksi (id_pesanan, tanggal_pembayaran, total, metode, status) 
                      VALUES (?, ?, ?, ?, ?)";
    $stmt_transaksi = $conn->prepare($sql_transaksi);
    $stmt_transaksi->bind_param('ssdss', $id_pesanan, $tanggal_pembayaran, $total, $metode, $status);

    if ($stmt_transaksi->execute()) {
        // Update the status of the pesanan
        $sql_update_pesanan = "UPDATE pesanan SET status = ? WHERE id_pesanan = ?";
        $stmt_update_pesanan = $conn->prepare($sql_update_pesanan);
        $stmt_update_pesanan->bind_param('ss', $statusPesanan, $id_pesanan);

        if ($stmt_update_pesanan->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Transaksi berhasil disimpan dan status pesanan diperbarui!'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Transaksi berhasil disimpan, tetapi gagal memperbarui status pesanan. Error: ' . $stmt_update_pesanan->error));
        }
    } else {
        echo json_encode(array('success' => false, 'message' => 'Error: ' . $stmt_transaksi->error));
    }

    // Close statements
    $stmt_transaksi->close();
    $stmt_update_pesanan->close();
    $conn->close();
}
