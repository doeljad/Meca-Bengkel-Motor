
<?php
include('../../controller/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Query untuk mengambil semua produk
    $sql = "SELECT * FROM produk";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $produk = array();

        while ($row = $result->fetch_assoc()) {
            $produk[] = array(
                "id_produk" => $row['id_produk'],
                "nama" => $row['nama'],
                "deskripsi" => $row['deskripsi'],
                "harga" => $row['harga']
            );
        }

        echo json_encode(array(
            "success" => true,
            "data" => $produk
        ));
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Tidak ada produk ditemukan."
        ));
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid request method."
    ));
}

// Menutup koneksi
$conn->close();
?>
