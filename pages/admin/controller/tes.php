<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../controller/connection.php');

// Ambil data dari input JSON
$input = file_get_contents('php://input');
error_log("Input yang diterima: " . $input);

// Coba untuk mendekode JSON
$data = json_decode($input, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    // JSON tidak valid, atau tidak ada data yang diterima
    error_log("Gagal mendekode JSON. Error: " . json_last_error_msg());
    echo "Data keranjang kosong atau tidak diterima.";
    exit;
}
print_r($data);

// Data yang diperlukan dari JSON
$id_pelanggan = $data['id_pelanggan'] ?? '';
$tanggal_pesanan = $data['tanggal_pesanan'] ?? '';
$status = $data['status'] ?? '';
$keranjang = $data['keranjang'] ?? [];

// Pastikan $keranjang tidak kosong
if (empty($keranjang)) {
    echo "Data keranjang kosong atau tidak diterima.";
    exit;
}

// Lanjutkan dengan proses simpan pesanan...


// Menghitung total pesanan
$total = 0;
foreach ($keranjang as $item) {
    if (isset($item['jumlah']) && isset($item['harga'])) {
        $total += $item['jumlah'] * $item['harga'];
    } else {
        echo "Data keranjang tidak valid.";
        exit;
    }
}

// Mulai transaksi
$conn->begin_transaction();

try {
    // Simpan pesanan
    $sql = "INSERT INTO pesanan (id_pelanggan, tanggal_pesanan, status, total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $id_pelanggan, $tanggal_pesanan, $status, $total);
    $stmt->execute();

    // Dapatkan ID pesanan yang baru saja dimasukkan
    $id_pesanan = $stmt->insert_id;

    // Simpan item pesanan dan file
    $uploadDir = '../../upload/';

    // Pastikan direktori unggahan ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Loop over each item in the keranjang
    foreach ($keranjang as $item) {
        // Lakukan validasi dan pengolahan untuk setiap item dalam keranjang
        $productName = $item['nama_produk'];
        $productSize = $item['ukuran_produk'];

        // Pastikan $item['files'] ada dan merupakan array sebelum mengaksesnya
        if (isset($item['files']) && is_array($item['files'])) {
            foreach ($item['files'] as $file) {
                $fileName = basename($file);
                $targetPath = $uploadDir . $fileName;

                // Contoh memindahkan file yang diunggah ke direktori yang ditentukan
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    echo "File $fileName berhasil diunggah.<br>";
                } else {
                    echo "Gagal mengunggah file $fileName.<br>";
                }
            }
        } else {
            echo "Tidak ada file yang diunggah untuk produk $productName, ukuran $productSize.<br>";
        }
    }


    // Commit transaksi
    $conn->commit();
    echo "Pesanan berhasil ditambahkan.";
} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $conn->rollback();
    echo "Pesanan gagal ditambahkan: " . $e->getMessage();
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
