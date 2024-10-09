<?php
// Koneksi ke database
include('pages/controller/connection.php');

// Query untuk mendapatkan data transaksi dan pesanan
$query = "
    SELECT 
        transaksi.id_transaksi,
        transaksi.id_pesanan,
        transaksi.tanggal_pembayaran,
        transaksi.total AS total_transaksi,
        transaksi.metode,
        transaksi.status AS status_transaksi,
        pesanan.id_pelanggan,
        pesanan.tanggal_pesanan,
        pesanan.tanggal_pengiriman,
        pesanan.status AS status_pesanan,
        pesanan.total AS total_pesanan
    FROM 
        transaksi
    JOIN 
        pesanan ON transaksi.id_pesanan = pesanan.id_pesanan
    ORDER BY 
        transaksi.tanggal_pembayaran DESC";

$result = $conn->query($query);
?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-2">Transaksi</h2>
                    <p class="card-description">Home / <code>Transaksi</code></p>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="table-responsive">
                            <table id="TransaksiTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>ID Pesanan</th>
                                        <th>ID Pelanggan</th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Total Transaksi</th>
                                        <th>Metode</th>
                                        <th>Status Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id_transaksi'] . "</td>";
                                            echo "<td>" . $row['id_pesanan'] . "</td>";
                                            echo "<td>" . $row['id_pelanggan'] . "</td>";
                                            echo "<td>" . $row['tanggal_pesanan'] . "</td>";
                                            echo "<td>" . $row['tanggal_pembayaran'] . "</td>";
                                            echo "<td>" . number_format($row['total_transaksi'], 2) . "</td>";
                                            echo "<td>" . $row['metode'] . "</td>";
                                            echo "<td>" . $row['status_transaksi'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='10'>Tidak ada data transaksi.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>
<?php
// Tutup koneksi
$conn->close();
?>
<script>
    $(document).ready(function() {
        $('#TransaksiTable').DataTable();
    });
</script>