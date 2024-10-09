<?php

include('../../controller/connection.php');
$sql = "SELECT id_kategori, nama_kategori FROM kategori";
$result = $conn->query($sql);

$kategori = array();

if ($result->num_rows > 0) {
    // Mengisi array dengan data kategori
    while ($row = $result->fetch_assoc()) {
        $kategori[] = array(
            "id" => $row["id_kategori"],
            "nama" => $row["nama_kategori"]
        );
    }
}

// Mengatur header agar mengembalikan JSON
header('Content-Type: application/json');
echo json_encode($kategori);

// Menutup koneksi
$conn->close();
