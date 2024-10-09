<?php
// Aktifkan logging error untuk debugging
error_log(print_r($_POST, true));
error_log(print_r($_FILES, true));

// Koneksi ke database (disesuaikan dengan koneksi Anda)
include('../../controller/connection.php');

// Ambil data dari form
$id_pelanggan = $_POST['id_pelanggan'];
$tanggal_pesanan = date('Y-m-d');
$status = 1;
$total = $_POST['total'];
$keranjang = isset($_POST['keranjang']) ? json_decode($_POST['keranjang'], true) : [];

// Masukkan data pesanan ke tabel pesanan
$sql_pesanan = "INSERT INTO pesanan (id_pelanggan, tanggal_pesanan, status, total) 
                VALUES ('$id_pelanggan', '$tanggal_pesanan', '$status', '$total')";
if ($conn->query($sql_pesanan) === TRUE) {
    $id_pesanan = $conn->insert_id; // Ambil ID pesanan yang baru saja dimasukkan

    if (!empty($keranjang)) {
        // Masukkan data produk dalam keranjang ke tabel detail_pesanan
        foreach ($keranjang as $produk) {
            $id_produk = $produk['id_produk'];
            $ukuran = $produk['ukuran'];
            $jumlah = $produk['jumlah'];
            $catatan = $conn->real_escape_string($produk['catatan']); // Escape untuk menghindari SQL Injection

            // Masukkan data produk ke dalam tabel detail_pesanan
            $sql_produk = "INSERT INTO detail_pesanan (id_pesanan, id_produk, ukuran, jumlah, catatan) 
                           VALUES ('$id_pesanan', '$id_produk', '$ukuran', '$jumlah', '$catatan')";
            if ($conn->query($sql_produk) === TRUE) {
                // Berhasil dimasukkan, lanjutkan ke file-file terlampir (jika ada)
                $id_produk_pesanan = $conn->insert_id;

                if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
                    foreach ($_FILES['files']['name'] as $index => $file_name) {
                        $file_tmp = $_FILES['files']['tmp_name'][$index];
                        $file_size = $_FILES['files']['size'][$index];
                        $file_error = $_FILES['files']['error'][$index];
                        $file_type = $_FILES['files']['type'][$index];

                        // Tentukan direktori penyimpanan
                        $file_destination = '../../controller/' . basename($file_name);

                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            // Masukkan data file ke dalam tabel pesanan_files
                            $sql_file = "INSERT INTO pesanan_files (id_detail_pesanan, file_name) 
                                         VALUES ('$id_produk_pesanan', '$file_name')";
                            if ($conn->query($sql_file) === TRUE) {
                                $id_file = $conn->insert_id;

                                // Update id_file di tabel detail_pesanan
                                $sql_update_produk = "UPDATE detail_pesanan SET id_file = '$id_file' 
                                                      WHERE id_produk_pesanan = '$id_produk_pesanan'";
                                $conn->query($sql_update_produk);
                            } else {
                                echo "Error: " . $sql_file . "<br>" . $conn->error;
                            }
                        } else {
                            echo "Error uploading file: " . $file_name;
                        }
                    }
                }
            } else {
                echo "Error: " . $sql_produk . "<br>" . $conn->error;
            }
        }
    }

    echo "Pesanan berhasil disimpan.";
} else {
    echo "Error: " . $sql_pesanan . "<br>" . $conn->error;
}

$conn->close();
