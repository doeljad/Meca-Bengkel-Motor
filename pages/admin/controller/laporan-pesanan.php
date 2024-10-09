<?php
include('../../controller/connection.php');
require_once '../../../assets/vendor/autoload.php'; // Sesuaikan path dengan struktur proyekmu

use Dompdf\Dompdf;
use Dompdf\Options;

// Validasi data dari form
$tanggal_awal = isset($_POST['tanggal_awal']) ? $_POST['tanggal_awal'] : null;
$tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : null;

// Validasi tanggal_awal dan tanggal_akhir tidak boleh kosong
if (empty($tanggal_awal) || empty($tanggal_akhir)) {
    die('Tanggal awal dan tanggal akhir harus diisi.');
}

// Query untuk mengambil data pesanan berdasarkan rentang tanggal
$query = "SELECT p.*, pp.*, pd.*
          FROM pesanan p
          INNER JOIN pesanan_produk pp ON p.id_pesanan = pp.id_pesanan
          INNER JOIN produk pd ON pd.id_produk = pp.id_produk
          INNER JOIN pelanggan plg ON plg.id_pelanggan = p.id_pelanggan
          WHERE p.tanggal_pesanan BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
$result = $conn->query($query);

// HTML untuk ditampilkan dalam PDF
$html = '<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Pesanan</h2>
    <table>
        <tr>
            <th>ID Pesanan</th>
            <th>ID Pelanggan</th>
            <th>Tanggal Pesanan</th>
            <th>Tanggal Pengiriman</th>
            <th>Status</th>
            <th>Total</th>
        </tr>';

while ($row_data = $result->fetch_assoc()) {
    $status = '';
    switch ($row_data['status']) {
        case 1:
            $status = 'Pesanan Masuk';
            break;
        case 2:
            $status = 'Diproses';
            break;
        case 3:
            $status = 'Dikirim';
            break;
        case 4:
            $status = 'Selesai';
            break;
        default:
            $status = 'Status Tidak Diketahui';
            break;
    }

    $html .= '<tr>
                <td>' . $row_data['id_pesanan'] . '</td>
                <td>' . $row_data['id_pelanggan'] . '</td>
                <td>' . $row_data['tanggal_pesanan'] . '</td>
                <td>' . $row_data['tanggal_pengiriman'] . '</td>
                <td>' . $status . '</td>
                <td>' . $row_data['total'] . '</td>
            </tr>';
}


$html .= '</table></body></html>';

// Buat objek Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Setting ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'landscape');

// Render PDF (menghasilkan file PDF)
$dompdf->render();

// Menyimpan dan menampilkan file PDF ke user
$dompdf->stream('laporan_pesanan.pdf', array('Attachment' => 0));

// Tutup koneksi dan exit
$conn->close();
exit;
