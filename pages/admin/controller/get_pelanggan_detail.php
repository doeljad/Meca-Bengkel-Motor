<?php
include('../../controller/connection.php');

if (isset($_GET['id_pelanggan'])) {
    $id_pelanggan = $_GET['id_pelanggan'];

    $sql = "SELECT nama, alamat, email FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $stmt->bind_result($nama, $alamat, $email);
    $stmt->fetch();

    $response = array(
        'nama' => $nama,
        'alamat' => $alamat,
        'email' => $email
    );

    echo json_encode($response);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array('success' => false, 'message' => 'ID pelanggan tidak valid'));
}
