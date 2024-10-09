<?php
require 'midtrans-config.php'; // Include Midtrans configuration
require '../../controller/connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pesanan']) && isset($_POST['aksi'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $aksi = $_POST['aksi'];

    if ($aksi == 'bayar') {
        // Ambil data pesanan
        $query_pesanan = $conn->prepare("SELECT total, id_pelanggan FROM pesanan WHERE id_pesanan = ?");
        $query_pesanan->bind_param('s', $id_pesanan);
        $query_pesanan->execute();
        $result_pesanan = $query_pesanan->get_result();
        $pesanan = $result_pesanan->fetch_assoc();

        if ($pesanan) {
            // Ambil data pelanggan
            $query_pelanggan = $conn->prepare("SELECT nama, email, no_telp FROM pelanggan WHERE id_pelanggan = ?");
            $query_pelanggan->bind_param('s', $pesanan['id_pelanggan']);
            $query_pelanggan->execute();
            $result_pelanggan = $query_pelanggan->get_result();
            $pelanggan = $result_pelanggan->fetch_assoc();

            // Buat transaksi Midtrans
            $transaction_details = array(
                'order_id' => $id_pesanan,
                'gross_amount' => $pesanan['total'], // Amount in IDR
            );

            $customer_details = array(
                'first_name'    => $pelanggan['nama'],
                'email'         => $pelanggan['email'],
                'phone'         => $pelanggan['no_telp']
            );

            $params = array(
                'transaction_details' => $transaction_details,
                'customer_details'    => $customer_details
            );

            // Menghasilkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Output JSON langsung
            echo json_encode(['snap_token' => $snapToken]);
            exit;
        } else {
            // Output JSON langsung
            echo json_encode(['error' => 'Pesanan tidak ditemukan.']);
            exit;
        }
    } elseif ($aksi == 'batalkan' || $aksi == 'diterima') {
        // Tentukan status berdasarkan aksi
        $status = ($aksi == 'diterima') ? 4 : 5;

        // Update status pesanan
        $query_update = "UPDATE pesanan SET status = ? WHERE id_pesanan = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param('is', $status, $id_pesanan);

        if ($stmt_update->execute()) {
            $message = ($aksi == 'diterima') ? 'Pesanan berhasil diterima.' : 'Pesanan berhasil dibatalkan.';
            echo "<script>
                alert('$message');
                window.location.href = '../../../index.php?page=pesanan';
            </script>";
        } else {
            echo "Terjadi kesalahan saat memperbarui status pesanan.";
        }
    }
}
if ($aksi == 'save_transaction' && isset($_POST['id_transaksi'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $tanggal_pembayaran = $_POST['tanggal_pembayaran'];
    $total = $_POST['total'];
    $metode = $_POST['metode'];
    $transaksi_status = $_POST['transaksi_status'];
    $status = $_POST['status'];

    // Save the transaction data in the transaksi table
    $query_save_transaksi = $conn->prepare("
        INSERT INTO transaksi (id_transaksi, id_pesanan, tanggal_pembayaran, total, metode, status) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $query_save_transaksi->bind_param('sssiss', $id_transaksi, $id_pesanan, $tanggal_pembayaran, $total, $metode, $transaksi_status);
    $query_save_transaksi->execute();

    // Update the status in the pesanan table
    $query_update_status = $conn->prepare("UPDATE pesanan SET status = ? WHERE id_pesanan = ?");
    $query_update_status->bind_param('is', $status, $id_pesanan);
    $query_update_status->execute();

    // Optionally, handle any errors or confirmation messages here
    echo "Transaction saved and status updated successfully.";
    exit;
}
