<?php
// session_start();
require_once '../../../assets/vendors/midtrans/Midtrans.php';
// Mengambil data JSON dari input
$jsonData = file_get_contents('php://input');
$dataCheckout = json_decode($jsonData, true);

\Midtrans\Config::$serverKey = 'SB-Mid-server-bGJzZgY1Ze4kzggfGG_S8G53';
\Midtrans\Config::$isProduction = false; // Set to true if in production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
