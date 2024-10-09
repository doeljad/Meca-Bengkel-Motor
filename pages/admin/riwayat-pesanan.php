<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('pages/controller/connection.php');

$success_message = ""; // Variabel untuk menyimpan pesan sukses
$error_message = "";   // Variabel untuk menyimpan pesan error

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Query untuk menghapus pesanan
        $sql = "DELETE FROM pesanan WHERE id_pesanan=$id";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Pesanan berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Query untuk memperbarui status pesanan
        $sql = "UPDATE pesanan SET status='$status' WHERE id_pesanan='$id'";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Status pesanan berhasil diperbarui!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Query untuk mengambil data pesanan
$sql = "SELECT 
    pesanan.id_pesanan,
    pesanan.tanggal_pesanan,
    pesanan.tanggal_pengiriman,
    pesanan.status,
    pesanan.total,
    pelanggan.nama AS nama_pelanggan,
    pelanggan.email AS email_pelanggan,
    pelanggan.no_telp AS telp_pelanggan,
    pelanggan.alamat AS alamat_pelanggan,
    GROUP_CONCAT(produk.nama SEPARATOR ', ') AS nama_produk, -- Menggabungkan nama produk jika ada lebih dari satu
    kategori.nama_kategori,
    detail_pesanan.jumlah,
    detail_pesanan.catatan
FROM 
    pesanan
INNER JOIN 
    pelanggan ON pesanan.id_pelanggan = pelanggan.id_pelanggan
INNER JOIN 
    detail_pesanan ON pesanan.id_pesanan = detail_pesanan.id_pesanan
INNER JOIN 
    produk ON detail_pesanan.id_produk = produk.id_produk
INNER JOIN 
    kategori ON produk.id_kategori = kategori.id_kategori
WHERE 
    pesanan.status IN ('4','5')
GROUP BY 
    pesanan.id_pesanan; -- Mengelompokkan berdasarkan id_pesanan agar tidak ada duplikat
";

$result = $conn->query($sql);
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card-body">
                    <h2 class="mb-2">Riwayat Pesanan</h2>
                    <p class="card-description">Home / <code>Riwayat Pesanan</code></p>
                    <!-- <div class="d-flex justify-content-end mb-3">
                        <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                        ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPesananModal">
                                <i class="bi bi-plus-circle"></i> Tambah Pesanan
                            </button>
                           
                        <?php
                        endif
                        ?>
                    </div> -->
                    <div class="table-responsive">
                        <table id="pesananTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Produk yang Dipesan</th>
                                    <th>Alamat</th>
                                    <th>Tanggal Pesanan</th>
                                    <!-- <th>Tanggal Pengiriman</th> -->
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Catatan</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) : ?>
                                    <tr class="align-middle">
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['nama_pelanggan']; ?></td>
                                        <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $row['nama_produk']; ?></td>
                                        <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $row['alamat_pelanggan']; ?></td>


                                        <td><?= $row['tanggal_pesanan']; ?></td>
                                        <td><?= $row['total']; ?></td>
                                        <?php
                                        $status = $row["status"];
                                        if ($status == 4) {
                                            echo "<td>Selesai</td>";
                                        } elseif ($status == 5) {
                                        } else {
                                            echo "<td>Status tidak diketahui</td>";
                                        }
                                        ?>
                                        <td><?= $row['catatan']; ?></td>

                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($success_message) : ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: '<?= $success_message; ?>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php?page=pesanan-masuk';
                                }
                            });
                        </script>
                    <?php endif; ?>
                    <?php if ($error_message) : ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: '<?= $error_message; ?>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php?page=pesanan-masuk';
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan produk yang dipesan -->
    <!-- Modal -->
    <div class="modal fade" id="produkModal" tabindex="-1" role="dialog" aria-labelledby="produkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="produkModalLabel">Detail Produk yang Dipesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="produkModalBody">
                    <!-- Konten produk akan dimasukkan di sini -->
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Tambahkan CSS untuk menangani tampilan teks dalam modal */
        #produkModalBody {
            word-wrap: break-word;
            /* Membungkus kata yang panjang */
            overflow-wrap: break-word;
            /* Membungkus kata yang panjang */
        }

        /* Mengatur maksimum tinggi modal dan menambahkan scroll jika teks melebihi batas */
        .modal-body {
            max-height: 70vh;
            /* Sesuaikan tinggi maksimal modal sesuai kebutuhan */
            overflow-y: auto;
        }
    </style>


    <!-- Modal Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Status Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm" method="post" action="">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="id_pesanan" name="id" value="">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Masuk</option>
                                <option value="2">Diproses</option>
                                <option value="3">Dikirim</option>
                                <option value="4">Selesai</option>
                                <option value="5">Dibatalkan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah Pesanan Baru -->
    <div class="modal fade" id="tambahPesananModal" tabindex="-1" aria-labelledby="tambahPesananModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPesananModalLabel">Tambah Pesanan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahPesanan" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="status" value="1">
                        <input type="hidden" id="harga" name="harga">
                        <div class="mb-3">
                            <label for="id_pelanggan" class="form-label">Nama Pelanggan</label>
                            <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                                <!-- Options akan diisi dengan AJAX -->
                            </select>
                        </div>
                        <input type="hidden" id="customer_name" name="customer_name">
                        <div class="mb-3">
                            <label for="alamat_pelanggan" class="form-label">Alamat Pelanggan</label>
                            <textarea class="form-control" id="alamat_pelanggan" name="alamat_pelanggan" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="email_pelanggan" class="form-label">Email Pelanggan</label>
                            <input class="form-control" id="email_pelanggan" name="email_pelanggan" rows="3" readonly></input>
                            <input type="hidden" id="nama_pelanggan" name="nama_pelanggan"></input>
                        </div>
                        <div class="mb-3">
                            <label for="produk" class="form-label">Produk</label>
                            <select class="form-select" id="produk">
                                <!-- Options akan diisi dengan AJAX -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" id="ukuran" name="ukuran">
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah">
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                        </div>
                        <label for="files" class="form-label me-2">Unggah File</label>
                        <div class="mb-3 d-flex align-items-center">
                            <input type="file" class="form-control me-2" id="files" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.png" style="width:auto;">
                            <button type="button" id="uploadFiles" class="btn btn-primary">Upload Files</button>
                        </div>
                        <div class="mb-3">
                            <label for="keranjang" class="form-label">Keranjang</label>
                            <ul id="keranjang" name="keranjang" class="list-group">
                                <!-- Produk yang ditambahkan akan muncul di sini -->
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" class="form-control" id="total" name="total" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="button" id="btnTambahProduk" class="btn btn-primary">Tambah Produk</button>
                            <button type="button" id="btnSimpanPesanan" class="btn btn-primary">Simpan Pesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var uploadButton = document.getElementById('uploadFiles');
            var fileInput = document.getElementById('files');

            fileInput.addEventListener('change', function() {
                // Aktifkan tombol upload saat pengguna memilih file baru
                uploadButton.disabled = false;
            });

            uploadButton.addEventListener('click', function() {
                uploadFiles();
            });
        });


        $(document).ready(function() {
            var keranjang = [];
            var uploadedFiles = [];

            $('#btnTambahProduk').click(function() {
                tambahProduk();
            });

            $('#btnSimpanPesanan').click(function() {
                simpanData();
            });

            $('#uploadFiles').click(function() {
                uploadFiles();
            });



            function uploadFiles() {
                var files = document.getElementById('files').files;
                var uploadButton = document.getElementById('uploadFiles');

                if (files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Harap pilih file untuk diunggah.'
                    });
                    return;
                }

                uploadButton.disabled = true; // Menonaktifkan tombol upload setelah diklik

                var formData = new FormData();
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }

                $.ajax({
                    url: 'pages/admin/controller/upload_files.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.success) {
                            uploadedFiles = res.files; // Menyimpan nama file yang diunggah
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'File berhasil diunggah.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal mengunggah file: ' + res.message
                            });
                            uploadButton.disabled = false; // Mengaktifkan kembali tombol upload jika gagal
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengunggah file.'
                        });
                        uploadButton.disabled = false; // Mengaktifkan kembali tombol upload jika terjadi error
                    }
                });
            }

            function tambahProduk() {
                var uploadButton = document.getElementById('uploadFiles');
                var id_produk = $('#produk').val();
                var nama_produk = $('#produk option:selected').text();
                var ukuran_produk = $('#ukuran').val().trim();
                var harga_produk = parseFloat($('#produk option:selected').data('harga'));
                var jumlah = $('#jumlah').val();
                var catatan = $('#catatan').val();

                if (ukuran_produk === '' || isNaN(parseFloat(jumlah)) || parseFloat(jumlah) <= 0) {
                    alert('Harap lengkapi ukuran dan jumlah yang dipesan.');
                    return;
                }

                var produk = {
                    id_produk: id_produk,
                    nama_produk: nama_produk,
                    ukuran_produk: ukuran_produk,
                    harga: harga_produk,
                    jumlah: jumlah,
                    catatan: catatan,
                    files: uploadedFiles, // Menggunakan nama file yang diunggah
                    file_count: uploadedFiles.length
                };

                keranjang.push(produk);
                renderKeranjang();
                $('#ukuran').val('');
                $('#jumlah').val('');
                $('#catatan').val('');
                $('#files').val('');
                uploadedFiles = []; // Reset uploaded files
                updateTotal();
                uploadButton.disabled = false;
            }

            function simpanData() {
                var form = $('#formTambahPesanan')[0];
                var formData = new FormData(form);

                if (keranjang.length === 0) {
                    alert('Keranjang masih kosong. Harap tambahkan produk terlebih dahulu.');
                    return;
                }

                formData.append('keranjang', JSON.stringify(keranjang));

                $.ajax({
                    url: 'pages/admin/controller/simpan-pesanan.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response); // Tambahkan ini untuk melihat respons dari server
                        if (response.success) {
                            $('#tambahPesananModal').modal('hide');
                            snap.pay(response.snapToken, {
                                onSuccess: function(result) {
                                    console.log('Success:', result);
                                    saveTransaction(response.pesananId, result, 1);
                                },
                                onPending: function(result) {
                                    console.log('Pending:', result);
                                    saveTransaction(response.pesananId, result, 0);
                                },
                                onError: function(result) {
                                    console.log('Error:', result);
                                    saveTransaction(response.pesananId, result, 0);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Gagal memproses pembayaran. Silakan coba lagi.'
                                    });
                                },
                                onClose: function() {
                                    saveTransaction(response.pesananId, result, 0);
                                    console.log('User closed the popup without finishing the payment');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menyimpan pesanan: ' + response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menambahkan pesanan. Silakan coba lagi.'
                        });
                    }
                });
            }




            function saveTransaction(pesananId, paymentResult, statusPesanan) {
                var transactionData = {
                    id_pesanan: pesananId,
                    total: paymentResult.gross_amount,
                    metode: paymentResult.payment_type,
                    status: paymentResult.transaction_status,
                    statusPesanan: statusPesanan
                };

                $.ajax({
                    url: 'pages/admin/controller/simpan-transaksi.php',
                    type: 'POST',
                    data: transactionData,
                    success: function(response) {
                        var responseData = JSON.parse(response);
                        if (responseData.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: responseData.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: responseData.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan transaksi. Silakan coba lagi.'
                        });
                    }
                });
            }


            window.hapusProduk = function(index) {
                keranjang.splice(index, 1);
                renderKeranjang();
                updateTotal();
            };

            function updateTotal() {
                var total = 0;
                keranjang.forEach(function(item) {
                    total += item.jumlah * item.harga;
                });
                $('#total').val(total);
            }

            function renderKeranjang() {
                var keranjangList = $('#keranjang');
                keranjangList.empty();

                keranjang.forEach(function(item, index) {
                    var subtotal = item.harga * item.jumlah;
                    var listItem =
                        '<li class="list-group-item d-flex justify-content-between align-items-start">' +
                        '<div class="ms-2 me-auto">' +
                        '<div class="fw-bold">' + item.nama_produk + '</div>' +
                        'Ukuran: ' + item.ukuran_produk + '<br>' +
                        'Harga: Rp ' + item.harga.toLocaleString() + '<br>' +
                        'Jumlah: ' + item.jumlah + '<br>' +
                        'Catatan: ' + item.catatan + '<br>' +
                        'Files: ' + item.file_count + '<br>' +
                        'Subtotal: Rp ' + subtotal.toLocaleString() +
                        '</div>' +
                        '<button class="btn btn-sm btn-danger" onclick="hapusProduk(' + index + ')">Hapus</button>' +
                        '</li>';
                    keranjangList.append(listItem);
                });
            }


            $.ajax({
                url: 'pages/admin/controller/get_pelanggan.php',
                type: 'GET',
                success: function(response) {
                    var pelanggan = JSON.parse(response);
                    var selectPelanggan = $('#id_pelanggan');
                    pelanggan.forEach(function(p) {
                        selectPelanggan.append('<option value="' + p.id_pelanggan + '">' + p.nama + '</option>');
                    });
                }
            });

            // Event listener untuk perubahan pilihan pada select id_pelanggan
            $('#id_pelanggan').on('change', function() {
                var idPelanggan = $(this).val();
                if (idPelanggan) {
                    $.ajax({
                        url: 'pages/admin/controller/get_pelanggan_detail.php',
                        type: 'GET',
                        data: {
                            id_pelanggan: idPelanggan
                        },
                        success: function(response) {
                            var pelanggan = JSON.parse(response);
                            $('#alamat_pelanggan').val(pelanggan.alamat);
                            $('#nama_pelanggan').val(pelanggan.nama);
                            $('#email_pelanggan').val(pelanggan.email);
                        }
                    });
                } else {
                    $('#alamat_pelanggan').val('');
                    $('#nama_pelanggan').val('');
                    $('#email_pelanggan').val('');
                }
            });

            $.ajax({
                url: 'pages/admin/controller/get_produk.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    console.log(response); // Log response untuk debugging
                    if (response.success) {
                        var selectProduk = $('#produk');
                        var produk = response.data;

                        produk.forEach(function(p) {
                            selectProduk.append('<option value="' + p.id_produk + '" data-harga="' + p.harga + '">' + p.nama + ' - Harga: Rp ' + p.harga.toLocaleString() + '</option>');
                        });
                    } else {
                        console.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            $('#tanggal_pesanan').change(function() {
                var selectedDate = $(this).val();
                console.log(selectedDate);
            });

            $('#formTambahPesanan').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Check if keranjang is empty
                if (keranjang.length === 0) {
                    alert('Keranjang masih kosong. Harap tambahkan produk terlebih dahulu.');
                    return;
                }

                simpanData(); // Call the function to save data
            });

            // Initialize DataTables
            $('#pesananTable').DataTable();
        });

        function viewProduk(id_pesanan) {
            // Menggunakan AJAX untuk mengambil data produk yang dipesan
            $.ajax({
                url: 'pages/admin/controller/get_items.php',
                type: 'GET',
                data: {
                    id_pesanan: id_pesanan
                },
                success: function(response) {
                    // Mengisikan data ke dalam modal
                    $('#produkModalBody').html(response);
                    $('#produkModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data produk.'
                    });
                }
            });
        }


        function updateStatus(data) {
            $('#id_pesanan').val(data.id_pesanan);
            $('#status').val(data.status);
            $('#updateStatusModal').modal('show');
        }

        function deletePesanan(id_pesanan) {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Menggunakan AJAX untuk menghapus data pesanan
                    $.ajax({
                        url: 'pages/admin/controller/delete-items.php', // Ganti dengan file yang sesuai untuk menghapus pesanan
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: id_pesanan
                        },
                        success: function(response) {
                            var res = JSON.parse(response);
                            if (res.status === 'success') {
                                Swal.fire(
                                    'Dihapus!',
                                    res.message,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Muat ulang halaman setelah menghapus
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    res.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus pesanan.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>