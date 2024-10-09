<?php
include('../../controller/connection.php');

$sql = "SELECT id_pelanggan, nama FROM pelanggan";
$result = $conn->query($sql);

$pelanggan = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pelanggan[] = $row;
    }
}

echo json_encode($pelanggan);

$conn->close();
