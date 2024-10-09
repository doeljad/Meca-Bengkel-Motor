<?php
include("../../controller/connection.php");

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Query untuk menghapus pesanan
        $stmt = $conn->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param("s", $id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Pesanan berhasil dihapus!';
        } else {
            $response['status'] = 'error';
            $response['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
echo json_encode($response);
