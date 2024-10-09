<?php
include('../../controller/connection.php');

$id_produk = $_GET['id_produk'];

$sql = "SELECT harga FROM produk WHERE id_produk = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_produk);
$stmt->execute();
$stmt->bind_result($harga);
$stmt->fetch();

echo json_encode($harga);

$stmt->close();
$conn->close();
