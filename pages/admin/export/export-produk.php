<?php
require __DIR__ . '../../../../assets/vendors/autoload.php'; // Ganti dengan path ke autoload.php

include('../../controller/connection.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// Query untuk mendapatkan data produk
$query = "SELECT produk.*,kategori.nama_kategori FROM produk
JOIN kategori ON kategori.id_kategori=produk.id_kategori";
$result = $conn->query($query);

// Generate HTML content
$html = '<h2 style="text-align: center;">Laporan Produk</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
$html .= '<thead><tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Deskripsi</th><th>Harga</th><th>Stok</th></tr></thead><tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['id_produk']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['nama']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['nama_kategori']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['deskripsi']) . '</td>';
    $html .= '<td>' . number_format($row['harga'], 0, ',', '.') . '</td>';
    $html .= '<td>' . htmlspecialchars($row['stok']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Initialize Dompdf and generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Ganti dengan 'landscape' jika perlu
$dompdf->render();

// Stream the PDF to the browser
$dompdf->stream("Laporan_Produk.pdf", array("Attachment" => 1));
exit;
