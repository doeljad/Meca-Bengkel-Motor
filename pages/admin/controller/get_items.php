<?php
include('../../controller/connection.php');

if (isset($_GET['id_pesanan'])) {
    $id_pesanan = $_GET['id_pesanan'];
    $id_pesanan = $conn->real_escape_string($id_pesanan);

    $sql = "SELECT produk.nama AS nama_produk, 
                   detail_pesanan.ukuran AS ukuran_produk,
                   detail_pesanan.jumlah, 
                   detail_pesanan.catatan
            FROM pesanan 
            INNER JOIN detail_pesanan ON pesanan.id_pesanan = detail_pesanan.id_pesanan
            INNER JOIN produk ON detail_pesanan.id_produk = produk.id_produk
            WHERE pesanan.id_pesanan = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        die('Query error: ' . $conn->error);
    }

    $stmt->bind_param("s", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['nama_produk']) . '</td>
                    <td>' . htmlspecialchars($row['jumlah']) . '</td>
                    <td>' . htmlspecialchars($row['catatan']) . '</td>
                    <td>';
            echo '</td></tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>Data produk tidak ditemukan.</p>';
    }

    $stmt->close();
} else {
    echo '<p>Permintaan tidak valid.</p>';
}

$conn->close();
