<?php
include('../../controller/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle delete action
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Query untuk menghapus layanan
        $sql_delete = "DELETE FROM produk WHERE id_produk=$id";

        if ($conn->query($sql_delete) === TRUE) {
            $success_message = "Layanan berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql_delete . "<br>" . $conn->error;
        }
    }
}
