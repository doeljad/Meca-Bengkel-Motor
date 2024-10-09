<?php
require __DIR__ . '../../../../assets/vendors/autoload.php'; // Ganti dengan path ke autoload.php

include('../../controller/connection.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// Query untuk mendapatkan data pelanggan
$query = "SELECT id_pelanggan, id_user, nama, email, no_telp, alamat FROM pelanggan";
$result = $conn->query($query);

// Generate HTML content
$html = '<h2 style="text-align: center;">Laporan Pelanggan</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
$html .= '<thead><tr><th>ID PLG</th><th>ID User</th><th>Nama</th><th>No Telp</th><th>Alamat</th></tr></thead><tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['id_pelanggan']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['id_user']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['nama']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['no_telp']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['alamat']) . '</td>';
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
$dompdf->stream("Laporan_Pelanggan.pdf", array("Attachment" => 1));
exit;
