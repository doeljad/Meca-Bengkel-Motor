<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../controller/connection.php');

// Ambil data dari form
$id_pelanggan = $_POST['id_pelanggan'] ?? '';
$tanggal_pesanan = $_POST['tanggal_pesanan'] ?? '';
$status = $_POST['status'] ?? '';

// Decode keranjang dari JSON ke array asosiatif
if (isset($_POST['keranjang']) && !empty($_POST['keranjang'])) {
    $keranjang = json_decode($_POST['keranjang'], true);
} else {
    echo "Data keranjang kosong atau tidak diterima.";
    exit;
}

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
    $files = $_FILES['files'];

    // Pastikan direktori unggahan ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Loop over each item in the keranjang
    foreach ($keranjang as $index => $item) {
        $file_id = null;

        // Check if there are files associated with this item
        if (!empty($files['name'][$index])) {
            foreach ($files['name'][$index] as $fileKey => $fileName) {
                if ($files['error'][$index][$fileKey] === UPLOAD_ERR_OK) {
                    $targetFilePath = $uploadDir . basename($fileName);

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($files['tmp_name'][$index][$fileKey], $targetFilePath)) {
                        echo "File $fileName berhasil diunggah.<br>";

                        // Simpan informasi file ke database
                        $sql_file = "INSERT INTO pesanan_files (id_pesanan, file_name) VALUES (?, ?)";
                        $stmt_file = $conn->prepare($sql_file);
                        $stmt_file->bind_param('is', $id_pesanan, $fileName);
                        $stmt_file->execute();

                        // Get the id of the uploaded file
                        $file_id = $stmt_file->insert_id;
                    } else {
                        throw new Exception('Gagal mengunggah file: ' . $fileName);
                    }
                } else {
                    echo "File upload error for index $index, file $fileKey: " . $files['error'][$index][$fileKey] . "<br>";
                }
            }
        }

        // Insert the item into detail_pesanan with the file ID
        $sql_item = "INSERT INTO detail_pesanan (id_pesanan, id_produk, ukuran, jumlah, catatan, id_file) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);
        $stmt_item->bind_param('iiisis', $id_pesanan, $item['id_produk'], $item['ukuran_produk'], $item['jumlah'], $item['catatan'], $file_id);
        $stmt_item->execute();

        // Debug: print the SQL error if any
        if ($stmt_item->error) {
            echo "SQL Error: " . $stmt_item->error . "<br>";
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
