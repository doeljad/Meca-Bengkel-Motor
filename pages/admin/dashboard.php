<?php
include("pages/controller/connection.php");
$sql_pesanan_masuk = "SELECT COUNT(*) as count FROM pesanan WHERE status = 1";
$result_pesanan_masuk = $conn->query($sql_pesanan_masuk);
$row_pesanan_masuk = $result_pesanan_masuk->fetch_assoc();
$pesanan_masuk = $row_pesanan_masuk['count'];

$sql_pesanan_diproses = "SELECT COUNT(*) as count FROM pesanan WHERE status = 2";
$result_pesanan_diproses = $conn->query($sql_pesanan_diproses);
$row_pesanan_diproses = $result_pesanan_diproses->fetch_assoc();
$pesanan_diproses = $row_pesanan_diproses['count'];

$sql_pesanan_dikirim = "SELECT COUNT(*) as count FROM pesanan WHERE status = 3";
$result_pesanan_dikirim = $conn->query($sql_pesanan_dikirim);
$row_pesanan_dikirim = $result_pesanan_dikirim->fetch_assoc();
$pesanan_dikirim = $row_pesanan_dikirim['count'];

$sql_pesanan_selesai = "SELECT COUNT(*) as count FROM pesanan WHERE status = 4";
$result_pesanan_selesai = $conn->query($sql_pesanan_selesai);
$row_pesanan_selesai = $result_pesanan_selesai->fetch_assoc();
$pesanan_selesai = $row_pesanan_selesai['count'];

$sql_total_pesanan = "SELECT COUNT(*) as count FROM pesanan";
$result_total_pesanan = $conn->query($sql_total_pesanan);
$row_total_pesanan = $result_total_pesanan->fetch_assoc();
$total_pesanan = $row_total_pesanan['count'];

$produk = "SELECT COUNT(*) as count FROM produk";
$produk = $conn->query($produk);
$produk = $produk->fetch_assoc();
$produk = $produk['count'];

$sql_jumlah_pelanggan = "SELECT COUNT(*) as count FROM pelanggan";
$result_jumlah_pelanggan = $conn->query($sql_jumlah_pelanggan);
$row_jumlah_pelanggan = $result_jumlah_pelanggan->fetch_assoc();
$jumlah_pelanggan = $row_jumlah_pelanggan['count'];

$sql_jumlah_pendapatan = "SELECT SUM(total) as total FROM pesanan";
$result_jumlah_pendapatan = $conn->query($sql_jumlah_pendapatan);
$row_jumlah_pendapatan = $result_jumlah_pendapatan->fetch_assoc();
$jumlah_pendapatan = $row_jumlah_pendapatan['total'] ?? 0;

$sql = "SELECT MONTH(tanggal_pesanan) AS month, SUM(total) AS revenue 
        FROM pesanan 
        WHERE YEAR(tanggal_pesanan) = YEAR(CURDATE()) 
        GROUP BY MONTH(tanggal_pesanan)";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Mengambil tahun saat ini
$current_year = date('Y');

// Query untuk mendapatkan data penjualan per kategori per bulan pada tahun ini
$sql = "SELECT k.nama_kategori, MONTH(p.tanggal_pesanan) AS bulan, SUM(dp.jumlah) AS total_penjualan
        FROM kategori k
        INNER JOIN produk b ON k.id_kategori = b.id_kategori
        INNER JOIN detail_pesanan dp ON b.id_produk = dp.id_produk
        INNER JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
        WHERE YEAR(p.tanggal_pesanan) = $current_year
        GROUP BY k.nama_kategori, MONTH(p.tanggal_pesanan)";

$result = $conn->query($sql);

// Array untuk menyimpan hasil query
$data_penjualan = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_penjualan[] = array(
            "kategori" => $row['nama_kategori'],
            "bulan" => $row['bulan'],
            "total_penjualan" => $row['total_penjualan']
        );
    }
}
$conn->close(); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- Dashboard Cards -->
        <div class="row">
            <!-- Total Pesanan Minggu Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Minggu Ini <i class="mdi mdi-chart-line mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $pesanan_masuk; ?></h2>
                        <h6 class="card-text">Pesanan yang diterima minggu ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pesanan Minggu Lalu -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Minggu Lalu <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $pesanan_diproses; ?></h2>
                        <h6 class="card-text">Pesanan yang diterima minggu lalu</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pesanan Bulan Ini -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan Bulan Ini <i class="mdi mdi-calendar mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $pesanan_dikirim; ?></h2>
                        <h6 class="card-text">Pesanan yang diterima bulan ini</h6>
                    </div>
                </div>
            </div>

            <!-- Total Pesanan -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pesanan <i class="mdi mdi-view-list mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $total_pesanan; ?></h2>
                        <h6 class="card-text">Jumlah total pesanan</h6>
                    </div>
                </div>
            </div>

            <!-- Total Produk -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-dark card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Produk <i class="mdi mdi-settings mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $produk; ?></h2>
                        <h6 class="card-text">Jumlah total Produk</h6>
                    </div>
                </div>
            </div>

            <!-- Jumlah Pelanggan -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Jumlah Pelanggan <i class="mdi mdi-people mdi-24px float-end"></i></h4>
                        <h2 class="mb-5"><?= $jumlah_pelanggan; ?></h2>
                        <h6 class="card-text">Jumlah total pelanggan</h6>
                    </div>
                </div>
            </div>

            <!-- Jumlah Pendapatan -->
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Jumlah Pendapatan <i class="mdi mdi-credit-card mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">Rp<?= number_format($jumlah_pendapatan); ?></h2>
                        <h6 class="card-text">Total pendapatan</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <!-- Penjualan Berdasarkan Kategori -->
            <div class="col-md-7 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penjualan Berdasarkan Kategori</h4>
                        <h6 class="card-subtitle">Pada Tahun <?= date('Y'); ?></h6>
                        <div class="chart-container mt-4">
                            <canvas id="chart-sales" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penghasilan Tahun Ini -->
            <div class="col-md-5 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penghasilan Tahun <?= date('Y'); ?></h4>
                        <h6 class="card-subtitle">Pendapatan kinerja penjualan berdasarkan bulan</h6>
                        <div class="chart-container mt-4">
                            <canvas id="chart-pendapatan" height="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <span class="text-muted">Copyright © <a href="https://www.bootstrapdash.com/" target="_blank">bootstrapdash.com </a>2020</span>
            <span class="text-muted float-end">Free <a href="https://www.bootstrapdash.com/material-design-dashboard/" target="_blank">material admin</a> dashboards from Bootstrapdash.com</span>
        </div>
    </footer>
</div>


<script>
    var revenueData = <?php echo json_encode($data); ?>;
    var dataPenjualan = <?php echo json_encode($data_penjualan); ?>;
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('chart-pendapatan').getContext('2d');
        var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var revenueData = <?php echo json_encode($data); ?>;

        // Prepare data for the chart
        var labels = revenueData.map(item => months[item.month - 1]);
        var data = revenueData.map(item => item.revenue);

        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data PHP dari PHP
        var dataPenjualan = <?php echo json_encode($data_penjualan); ?>;
        var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Siapkan data untuk chart
        var labels = months; // Gunakan label bulan
        var datasets = {};

        // Buat struktur data untuk setiap kategori
        dataPenjualan.forEach(function(item) {
            if (!datasets[item.kategori]) {
                datasets[item.kategori] = Array(12).fill(0); // Inisialisasi data untuk 12 bulan
            }
            datasets[item.kategori][item.bulan - 1] = item.total_penjualan;
        });

        // Siapkan data dalam format yang sesuai untuk Chart.js
        var chartData = {
            labels: labels,
            datasets: []
        };

        // Tambahkan setiap kategori sebagai dataset
        for (var kategori in datasets) {
            chartData.datasets.push({
                label: kategori,
                data: datasets[kategori],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            });
        }

        // Buat chart menggunakan Chart.js
        var ctx = document.getElementById('chart-sales').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,

                    }
                }
            }
        });
    });
</script>