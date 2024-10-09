<?php
include('../../controller/connection.php');

$response = array();
header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start(); // Start output buffering

    $action = $_POST['action'];
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($action === 'update') {
        // Query untuk memperbarui produk
        $sql_update = "UPDATE produk SET nama='$nama', id_kategori='$kategori', deskripsi='$deskripsi', harga=$harga, stok=$stok WHERE id_produk=$id";

        if ($conn->query($sql_update) === TRUE) {
            $produk_id = $id;
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $conn->error;
            ob_end_clean(); // Clean output buffer
            echo json_encode($response);
            exit;
        }
    } elseif ($action === 'create') {
        // Query untuk menambahkan produk baru
        $sql_insert = "INSERT INTO produk (nama, id_kategori, deskripsi, harga, stok) VALUES ('$nama', '$kategori', '$deskripsi', $harga, $stok)";

        if ($conn->query($sql_insert) === TRUE) {
            $produk_id = $conn->insert_id; // Dapatkan ID produk yang baru ditambahkan
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $conn->error;
            ob_end_clean(); // Clean output buffer
            echo json_encode($response);
            exit;
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Action tidak valid.';
        ob_end_clean(); // Clean output buffer
        echo json_encode($response);
        exit;
    }

    // Proses unggahan gambar jika ada
    if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'][0])) {
        $files = $_FILES['gambar'];
        $uploadedFiles = array();
        $upload_directory = '../../../assets/images/produk/';

        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }

        // Hapus gambar yang sudah ada dari database jika ada
        $sql_select_images = "SELECT nama_file FROM gambar_produk WHERE id_produk=$produk_id";
        $result_images = $conn->query($sql_select_images);

        if ($result_images->num_rows > 0) {
            while ($row = $result_images->fetch_assoc()) {
                $existing_image = $row['nama_file'];
                $existing_image_path = $upload_directory . $existing_image;

                if (file_exists($existing_image_path)) {
                    unlink($existing_image_path); // Hapus gambar lama dari server
                }
            }
            $sql_delete_images = "DELETE FROM gambar_produk WHERE id_produk=$produk_id";
            $conn->query($sql_delete_images);
        }

        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileError = $files['error'][$i];

            if ($fileError === 0) {
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileNameNew = uniqid('', true) . '.' . $fileExt;
                $fileDestination = $upload_directory . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $uploadedFiles[] = $fileNameNew;

                    // Menyimpan informasi file ke dalam database
                    $sql = "INSERT INTO gambar_produk (id_produk, nama_file) VALUES ($produk_id, '$fileNameNew')";
                    if ($conn->query($sql) === FALSE) {
                        $response['success'] = false;
                        $response['message'] = 'Error: ' . $conn->error;
                        ob_end_clean(); // Clean output buffer
                        echo json_encode($response);
                        exit;
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Gagal memindahkan file.';
                    ob_end_clean(); // Clean output buffer
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Error saat mengunggah file.';
                ob_end_clean(); // Clean output buffer
                echo json_encode($response);
                exit;
            }
        }

        $response['success'] = true;
        $response['files'] = $uploadedFiles;
    } else {
        // Jika tidak ada gambar baru, pertahankan gambar yang ada
        $response['success'] = true;
        $response['message'] = 'Gambar tidak diunggah, gambar sebelumnya dipertahankan.';
    }

    ob_end_clean(); // Clean output buffer
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = 'Metode permintaan tidak valid.';
    echo json_encode($response);
}
