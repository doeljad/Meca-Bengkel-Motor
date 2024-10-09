<?php require __DIR__ . '../../../../assets/vendors/autoload.php'; // Ganti dengan path ke autoload.php

include('../../controller/connection.php');

use Dompdf\Dompdf;
use Dompdf\Options;

$statusText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = $_POST['generate_report'];

    // Kriteria laporan berdasarkan bulan
    if ($reportType === 'monthly') {
        $month = $_POST['report_month'];
        $year = date('Y'); // Tahun saat ini
        $query = "SELECT * FROM pesanan WHERE MONTH(tanggal_pesanan) = ? AND YEAR(tanggal_pesanan) = ? AND status =4";
        $params = [$month, $year];
    }

    // Kriteria laporan berdasarkan tahun
    if ($reportType === 'yearly') {
        $year = $_POST['report_year'];
        $query = "SELECT * FROM pesanan WHERE YEAR(tanggal_pesanan) = ? AND status =4";
        $params = [$year];
    }

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Inisialisasi total pendapatan
    $totalPendapatan = 0;

    // Generate HTML content
    $html = '<h2 style="text-align: center;">';
    if ($reportType === 'monthly') {
        $html .= "Laporan Pesanan Bulanan - $month/$year";
    } else {
        $html .= "Laporan Pesanan Tahunan - $year";
    }
    $html .= '</h2>';
    $html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead><tr><th>ID Pesanan</th><th>Tanggal Pesanan</th><th>Total</th><th>Status</th></tr></thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        switch ($row['status']) {
            case '1':
                $statusText = 'Diterima';
                break;
            case '2':
                $statusText = 'Diproses';
                break;
            case '3':
                $statusText = 'Dikirim';
                break;
            case '4':
                $statusText = 'Selesai';
                break;
            default:
                $statusText = 'Tidak Diketahui';
                break;
        }
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['id_pesanan']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['tanggal_pesanan']) . '</td>';
        $html .= '<td>' . number_format($row['total'], 0, ',', '.') . '</td>';
        $html .= '<td>' . htmlspecialchars($statusText) . '</td>';
        $html .= '</tr>';

        // Tambahkan total ke pendapatan keseluruhan
        $totalPendapatan += $row['total'];
    }

    $html .= '</tbody></table>';

    // Tambahkan total pendapatan di akhir laporan
    $html .= '<h3 style="text-align: right;">Total Pendapatan: Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</h3>';

    // Initialize Dompdf and generate PDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait'); // Ganti dengan 'landscape' jika perlu
    $dompdf->render();

    // Stream the PDF to the browser
    $dompdf->stream("Laporan_Pesanan.pdf", array("Attachment" => 1));
    exit;
}
