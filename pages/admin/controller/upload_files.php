<?php
include('../../controller/connection.php');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['files'])) {
        $files = $_FILES['files'];
        $uploadedFiles = array();

        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileSize = $files['size'][$i];
            $fileError = $files['error'][$i];
            $fileType = $files['type'][$i];

            if ($fileError === 0) {
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileNameNew = uniqid('', true) . '.' . $fileExt;
                $fileDestination = '../../../assets/images/upload-file/' . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $uploadedFiles[] = $fileNameNew;

                    // Menyimpan informasi file ke dalam database
                    $sql = "INSERT INTO pesanan_files (file_name) VALUES ('$fileNameNew')";
                    $conn->query($sql);
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Gagal memindahkan file.';
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Error saat mengunggah file.';
                echo json_encode($response);
                exit;
            }
        }

        $response['success'] = true;
        $response['files'] = $uploadedFiles;
    } else {
        $response['success'] = false;
        $response['message'] = 'Tidak ada file yang diunggah.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Metode permintaan tidak valid.';
}

echo json_encode($response);
