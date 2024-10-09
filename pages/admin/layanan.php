<?php
include('pages/controller/connection.php');

$success_message = "";
$error_message = "";

// Handle POST request for CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle delete action
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Query untuk menghapus produk
        $sql_delete = "DELETE FROM produk WHERE id_produk = $id";

        if ($conn->query($sql_delete) === TRUE) {
            $success_message = "Produk berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql_delete . "<br>" . $conn->error;
        }
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">

            <div class="card-body">
                <h2 class="mb-2">Produk</h2>
                <p class="card-description">Home / <code>Produk</code></p>
                <div class="d-flex justify-content-end mb-3">
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                    ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahLayananModal" onclick="setActionCreate()">
                            <i class="bi bi-plus-circle"></i> Tambah Produk
                        </button>
                    <?php
                    endif
                    ?>
                </div>
                <div class="table-responsive">
                    <table id="layananTable" class="table ">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) : ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $result = $conn->query("SELECT p.*, k.nama_kategori AS nama_kategori, p.deskripsi AS produk_deskripsi FROM produk p INNER JOIN kategori k ON k.id_kategori = p.id_kategori");
                            while ($row = $result->fetch_assoc()) :
                                // Tentukan kelas baris berdasarkan stok
                                $row_class = ($row['stok'] < 5) ? 'table-danger' : (($row['stok'] < 10) ? 'table-warning' : '');
                            ?>
                                <tr class="align-middle <?= $row_class; ?>">
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                                    <td><?= htmlspecialchars(strlen($row['produk_deskripsi']) > 50 ? substr($row['produk_deskripsi'], 0, 50) . '...' : $row['produk_deskripsi']); ?></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars($row['stok']); ?></td>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) : ?>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editLayanan(<?= $row['id_produk']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteLayanan(<?= $row['id_produk']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    <?php endif; ?>
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
                        });
                    </script>
                <?php endif; ?>
                <?php if ($error_message) : ?>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?= $error_message; ?>'
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<form id="deleteLayananForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteLayananId">
</form>

<!-- Modal Tambah/Edit Produk -->
<div class="modal fade" id="tambahLayananModal" tabindex="-1" aria-labelledby="tambahLayananModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahLayananModalLabel">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="layananForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="modalAction" name="action">
                    <input type="hidden" id="pesananId" name="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-control" id="kategori" name="kategori" required>
                            <!-- Options will be populated by AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Unggah Gambar</label>
                        <input type="file" class="form-control" id="gambar" name="gambar[]" multiple>
                    </div>
                    <button type="button" id="unggahBtn" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#unggahBtn').click(function() {
            var formData = new FormData($('#layananForm')[0]);

            $.ajax({
                url: 'pages/admin/controller/simpan-produk.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Produk berhasil disimpan',
                        }).then(() => {
                            window.location.href = 'index.php?page=layanan';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal mengunggah produk: ' + response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengunggah produk.',
                    });
                }
            });
        });
    });

    function setActionCreate() {
        // Mengatur nilai input tersembunyi menjadi 'create'
        document.getElementById('modalAction').value = 'create';
        console.log("ditambahkan");

    }

    function editLayanan(id) {
        $.ajax({
            url: 'pages/admin/controller/get_layanan.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
            },
            success: function(response) {
                $('#tambahLayananModalLabel').text('Edit Produk');
                $('#modalAction').val('update');
                $('#pesananId').val(response.id_produk);
                $('#nama').val(response.nama);
                $('#kategori').val(response.kategori_id);
                $('#deskripsi').val(response.deskripsi);
                $('#harga').val(response.harga);
                $('#stok').val(response.stok); // Set stok value
                $('#tambahLayananModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function deleteLayanan(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Produk ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'pages/admin/controller/hapus-layanan.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Produk berhasil dihapus.'
                        }).then(() => {
                            window.location.href = 'index.php?page=layanan';
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus produk. Silakan coba lagi.'
                        });
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        $('#layananTable').DataTable();

        // Populate categories in the dropdown
        $.ajax({
            url: 'pages/admin/controller/get_kategori.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var kategoriSelect = $('#kategori');
                kategoriSelect.empty();
                response.forEach(function(kategori) {
                    kategoriSelect.append('<option value="' + kategori.id + '">' + kategori.nama + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

    });
</script>

<?php
$conn->close();
?>