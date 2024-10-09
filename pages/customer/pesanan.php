<?php
include('pages/controller/connection.php');
// session_start();

$id_user = $_SESSION['id_user'];

// Function to get orders by status
function getPesananByStatus($conn, $id_user, $statuses)
{
    // Convert statuses to an array if it is not already
    if (!is_array($statuses)) {
        $statuses = [$statuses];
    }

    // Prepare placeholders for the statuses
    $placeholders = implode(',', array_fill(0, count($statuses), '?'));

    $query = "
    SELECT 
        pesanan.id_pesanan,
        pesanan.tanggal_pesanan,
        pesanan.total,
        pesanan.status
    FROM 
        pesanan 
    INNER JOIN 
        pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
    WHERE 
        pelanggan.id_user = ? AND pesanan.status IN ($placeholders)
    ORDER BY 
        pesanan.tanggal_pesanan DESC;
    ";

    $stmt = $conn->prepare($query);

    // Bind parameters dynamically
    $types = str_repeat('i', count($statuses) + 1);
    $params = array_merge([$id_user], $statuses);
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    return $stmt->get_result();
}

// Fetch orders based on status
$pesananBelumBayar = getPesananByStatus($conn, $id_user, 0); #Belum Dibayar
$pesananDikemas = getPesananByStatus($conn, $id_user, [1, 2]); #Dikemas or Dikirim
$pesananDikirim = getPesananByStatus($conn, $id_user, 3); #Dikirim
$pesananSelesai = getPesananByStatus($conn, $id_user, 4); #Selesai
$pesananDibatalkan = getPesananByStatus($conn, $id_user, 5); #Dibatalkan
?>

<br>
<div class="container">
    <h2 class="text-center">Histori Pesanan</h2>
    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="belum-bayar-tab" data-toggle="tab" href="#belum-bayar" role="tab" aria-controls="belum-bayar" aria-selected="true">
                <i class="fas fa-credit-card"></i> Belum Bayar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dikemas-tab" data-toggle="tab" href="#dikemas" role="tab" aria-controls="dikemas" aria-selected="false">
                <i class="fas fa-box"></i> Dikemas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dikirim-tab" data-toggle="tab" href="#dikirim" role="tab" aria-controls="dikirim" aria-selected="false">
                <i class="fas fa-truck"></i> Dikirim
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="selesai-tab" data-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">
                <i class="fas fa-check"></i> Selesai
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dibatalkan-tab" data-toggle="tab" href="#dibatalkan" role="tab" aria-controls="dibatalkan" aria-selected="false">
                <i class="fas fa-times"></i> Dibatalkan
            </a>
        </li>

    </ul>
    <div class="tab-content mt-3" id="myTabContent">
        <!-- Belum Bayar Tab -->
        <div class="tab-pane fade show active" id="belum-bayar" role="tabpanel" aria-labelledby="belum-bayar-tab">
            <?php
            $result = $pesananBelumBayar;
            include 'pages/customer/pesanan_status.php';
            ?>
        </div>
        <!-- Dikemas Tab -->
        <div class="tab-pane fade" id="dikemas" role="tabpanel" aria-labelledby="dikemas-tab">
            <?php
            $result = $pesananDikemas;
            include 'pages/customer/pesanan_status.php';
            ?>
        </div>
        <!-- Dikirim Tab -->
        <div class="tab-pane fade" id="dikirim" role="tabpanel" aria-labelledby="dikirim-tab">
            <?php
            $result = $pesananDikirim;
            include 'pages/customer/pesanan_status.php';
            ?>
        </div>
        <!-- Selesai Tab -->
        <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai-tab">
            <?php
            $result = $pesananSelesai;
            include 'pages/customer/pesanan_status.php';
            ?>
        </div>
        <!-- dibatalkan Tab -->
        <div class="tab-pane fade" id="dibatalkan" role="tabpanel" aria-labelledby="dibatalkan-tab">
            <?php
            $result = $pesananDibatalkan;
            include 'pages/customer/pesanan_status.php';
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>