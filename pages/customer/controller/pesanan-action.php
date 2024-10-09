<?php
require 'midtrans-config.php'; // Include Midtrans configuration
require '../../controller/connection.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pesanan']) && isset($_POST['aksi'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $aksi = $_POST['aksi'];

    if ($aksi == 'bayar') {
        // Ambil data pesanan
        $query_pesanan = $conn->prepare("SELECT total, id_pelanggan FROM pesanan WHERE id_pesanan = ?");
        $query_pesanan->bind_param('s', $id_pesanan);
        $query_pesanan->execute();
        $result_pesanan = $query_pesanan->get_result();
        $pesanan = $result_pesanan->fetch_assoc();

        if ($pesanan) {
            // Ambil data pelanggan
            $query_pelanggan = $conn->prepare("SELECT nama, email, no_telp FROM pelanggan WHERE id_pelanggan = ?");
            $query_pelanggan->bind_param('s', $pesanan['id_pelanggan']);
            $query_pelanggan->execute();
            $result_pelanggan = $query_pelanggan->get_result();
            $pelanggan = $result_pelanggan->fetch_assoc();

            // Buat transaksi Midtrans
            $transaction_details = array(
                'order_id' => 'byr' . $id_pesanan,
                'gross_amount' => $pesanan['total'], // Amount in IDR
            );

            $customer_details = array(
                'first_name'    => $pelanggan['nama'],
                'email'         => $pelanggan['email'],
                'phone'         => $pelanggan['no_telp']
            );

            $params = array(
                'transaction_details' => $transaction_details,
                'customer_details'    => $customer_details
            );

            // Menghasilkan Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Output JSON langsung
            echo json_encode(['snap_token' => $snapToken]);
            exit;
        } else {
            // Output JSON langsung
            echo json_encode(['error' => 'Pesanan tidak ditemukan.']);
            exit;
        }
    } elseif ($aksi == 'batalkan' || $aksi == 'diterima') {
        // Tentukan status berdasarkan aksi
        $status = ($aksi == 'diterima') ? 4 : 5;

        // Update status pesanan
        $query_update = "UPDATE pesanan SET status = ? WHERE id_pesanan = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param('is', $status, $id_pesanan);

        if ($stmt_update->execute()) {
            $message = ($aksi == 'diterima') ? 'Pesanan berhasil diterima.' : 'Pesanan berhasil dibatalkan.';
            echo "<script>
                alert('$message');
                window.location.href = '../../../index.php?page=pesanan';
            </script>";
        } else {
            echo "Terjadi kesalahan saat memperbarui status pesanan.";
        }
    } elseif ($aksi == 'review' && isset($_POST['id_produk'], $_POST['rating'], $_POST['review_text'], $_POST['id_detail_pesanan'])) {
        // Handle the review action
        $id_produk = $_POST['id_produk'];
        $id_user = $_SESSION['id_user']; // Assuming the user is logged in and their ID is stored in the session
        $id_detail_pesanan = $_POST['id_detail_pesanan'];
        $rating = $_POST['rating'];
        $review_text = $_POST['review_text'];
        $created_at = date('Y-m-d H:i:s'); // Current timestamp

        // Prepare and execute the review insertion
        $query_review = $conn->prepare("
            INSERT INTO reviews (id_produk, id_user, id_detail_pesanan, rating, review_text, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $query_review->bind_param('siiiss', $id_produk, $id_user, $id_detail_pesanan, $rating, $review_text, $created_at);

        if ($query_review->execute()) {
            // Get the last inserted review ID
            $review_id = $conn->insert_id;

            // Handle file uploads
            if (!empty($_FILES['media']['name'][0])) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
                $upload_dir = '../../../assets/images/upload-file/';
                $files = $_FILES['media'];

                // Create the upload directory if it does not exist
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                        echo "Failed to create directory: $upload_dir";
                        exit;
                    }
                }

                foreach ($files['name'] as $key => $file_name) {
                    $file_tmp = $files['tmp_name'][$key];
                    $file_type = $files['type'][$key];
                    $file_size = $files['size'][$key];
                    $file_error = $files['error'][$key];

                    // Debugging
                    if ($file_error !== UPLOAD_ERR_OK) {
                        echo "File upload error: $file_error";
                        continue;
                    }

                    if (!in_array($file_type, $allowed_types)) {
                        echo "File type not allowed: $file_type";
                        continue;
                    }

                    // Validate file size (example: 10MB max)
                    if ($file_size > 10485760) {
                        echo "File size exceeds the limit of 10MB.";
                        continue;
                    }

                    $new_file_name = uniqid() . '-' . basename($file_name);
                    $target_file = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        // Insert file information into the gambar_review table
                        $query_file = $conn->prepare("
                            INSERT INTO gambar_review (id_review, nama_file, tanggal_ditambahkan) 
                            VALUES (?, ?, ?)
                        ");
                        $tanggal_ditambahkan = date('Y-m-d H:i:s');
                        $query_file->bind_param('iss', $review_id, $new_file_name, $tanggal_ditambahkan);

                        if ($query_file->execute()) {
                            // echo "File uploaded and data inserted successfully";
                        } else {
                            echo "Error inserting file info into database: " . $query_file->error;
                        }
                    } else {
                        echo "Failed to move uploaded file: $file_name";
                    }
                }
            } else {
                echo "No file uploaded.";
            }

            echo "<script>
                alert('Review berhasil ditambahkan.');
                window.location.href = '../../../index.php?page=pesanan';
            </script>";
        } else {
            echo "Error adding review: " . $query_review->error;
        }
    } elseif ($aksi == 'save_transaction' && isset($_POST['id_transaksi'])) {
        $id_transaksi = $_POST['id_transaksi'];
        $tanggal_pembayaran = $_POST['tanggal_pembayaran'];
        $total = $_POST['total'];
        $metode = $_POST['metode'];
        $transaksi_status = $_POST['transaksi_status'];
        $status = $_POST['status'];

        // Save the transaction data in the transaksi table
        $query_save_transaksi = $conn->prepare("
            INSERT INTO transaksi (id_transaksi, id_pesanan, tanggal_pembayaran, total, metode, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $query_save_transaksi->bind_param('sssiss', $id_transaksi, $id_pesanan, $tanggal_pembayaran, $total, $metode, $transaksi_status);
        $query_save_transaksi->execute();

        // Update the status in the pesanan table
        $query_update_status = $conn->prepare("UPDATE pesanan SET status = ? WHERE id_pesanan = ?");
        $query_update_status->bind_param('is', $status, $id_pesanan);
        $query_update_status->execute();

        // Optionally, handle any errors or confirmation messages here
        echo "Transaction saved and status updated successfully.";
        exit;
    }
}
