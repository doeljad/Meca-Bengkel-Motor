<?php
include('../../controller/connection.php');
require_once dirname(__FILE__) . '../../../payment/midtrans/Midtrans.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-bGJzZgY1Ze4kzggfGG_S8G53';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $id_pelanggan = intval($_POST['id_pelanggan']);
    $total = floatval($_POST['total']);
    $status = '0'; // Pastikan $status adalah string jika kolom status di database adalah ENUM atau VARCHAR
    $tanggal_pesanan = date('Y-m-d H:i:s');
    $keranjang = json_decode($_POST['keranjang'], true);

    // Validate input
    if (empty($id_pelanggan) || empty($total) || empty($keranjang)) {
        $response['message'] = 'Data tidak lengkap.';
        echo json_encode($response);
        exit;
    }

    // Generate a unique id_pesanan
    $id_pesanan = bin2hex(random_bytes(16)); // Generate a 32-character hexadecimal string

    // Simpan pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_pesanan, id_pelanggan, tanggal_pesanan, `status`, total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $id_pesanan, $id_pelanggan, $tanggal_pesanan, $status, $total);

    if ($stmt->execute()) {
        foreach ($keranjang as $item) {
            $id_produk = intval($item['id_produk']);
            $ukuran = $item['ukuran_produk'];
            $jumlah = intval($item['jumlah']);
            $catatan = $item['catatan'];
            $files = $item['files'];

            // Simpan detail pesanan
            $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, ukuran, jumlah, catatan) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiis", $id_pesanan, $id_produk, $ukuran, $jumlah, $catatan);

            if ($stmt->execute()) {
                $id_detail_pesanan = $stmt->insert_id;

                // Simpan file pesanan
                if (!empty($files)) {
                    foreach ($files as $file) {
                        // Update file pesanan
                        $stmt = $conn->prepare("UPDATE pesanan_files SET id_detail_pesanan = ? WHERE file_name = ?");
                        $stmt->bind_param("is", $id_detail_pesanan, $file);
                        $stmt->execute();
                    }
                }
            }
        }

        // Prepare transaction details for Midtrans
        $transactionDetails = array(
            'order_id' => $id_pesanan,
            'gross_amount' => $total
        );

        // Prepare customer details
        $customerDetails = array(
            'first_name' => $_POST['nama_pelanggan'] ?? '',
            'email' => $_POST['email_pelanggan'] ?? ''
        );

        // Detail untuk debugging
        error_log("Transaction Details: " . json_encode($transactionDetails));
        error_log("Customer Details: " . json_encode($customerDetails));

        // Create transaction
        $transaction = array(
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails
        );

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transaction);
            error_log("Snap Token: " . $snapToken); // Log Snap Token
            $response['success'] = true;
            $response['snapToken'] = $snapToken;
            $response['pesananId'] = $id_pesanan; // Use id_pesanan
        } catch (Exception $e) {
            error_log("Midtrans Error: " . $e->getMessage()); // Log error message
            $response['message'] = 'Gagal membuat Snap Token: ' . $e->getMessage();
        }

        // Cleanup old files
        $stmt = $conn->prepare("DELETE FROM pesanan_files WHERE id_detail_pesanan = 0");
        $stmt->execute();
    } else {
        $response['message'] = 'Gagal menyimpan pesanan: ' . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $response['message'] = 'Metode permintaan tidak valid.';
}

header('Content-Type: application/json');
echo json_encode($response);
