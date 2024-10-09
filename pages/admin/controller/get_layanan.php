<?php
include('../../controller/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Query untuk mengambil data layanan berdasarkan ID
        $sql = "SELECT * FROM produk WHERE id_produk = ?";

        // Mempersiapkan statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameter
            $stmt->bind_param("i", $id);

            // Menjalankan query
            $stmt->execute();

            // Mengambil hasil
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Mengambil data layanan
                $row = $result->fetch_assoc();
                echo json_encode(array(
                    "success" => true,
                    "id_produk" => $row['id_produk'],
                    "nama" => $row['nama'],
                    "kategori_id" => $row['id_kategori'],
                    "deskripsi" => $row['deskripsi'],
                    "harga" => $row['harga'],
                    "stok" => $row['stok']
                ));
            } else {
                echo json_encode(array("success" => false, "message" => "Layanan tidak ditemukan."));
            }

            // Menutup statement
            $stmt->close();
        } else {
            echo json_encode(array("success" => false, "message" => "Gagal mempersiapkan statement."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "ID tidak ditemukan."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request method."));
}

// Menutup koneksi
$conn->close();
