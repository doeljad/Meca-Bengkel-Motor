<?php
include('pages/controller/connection.php');

$success_message = ""; // Variabel untuk menyimpan pesan sukses
$error_message = "";   // Variabel untuk menyimpan pesan error

// Query untuk mengambil data kategori
$sql = "SELECT * FROM kategori";
$result = $conn->query($sql);

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];

        // Query untuk menghapus kategori
        $sql_delete_category = "DELETE FROM kategori WHERE id_kategori=$id";

        if ($conn->query($sql_delete_category) === TRUE) {
            $success_message = "Data kategori berhasil dihapus!";
        } else {
            $error_message = "Error: " . $sql_delete_category . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'add') {
        $nama_kategori = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];

        // Query untuk menambahkan kategori baru
        $sql_add_category = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";

        if ($conn->query($sql_add_category) === TRUE) {
            $success_message = "Data kategori berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql_add_category . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];

        // Query untuk mengupdate kategori
        $sql_update_category = "UPDATE kategori SET nama_kategori='$nama_kategori', deskripsi='$deskripsi' WHERE id_kategori=$id_kategori";

        if ($conn->query($sql_update_category) === TRUE) {
            $success_message = "Data kategori berhasil diupdate!";
        } else {
            $error_message = "Error: " . $sql_update_category . "<br>" . $conn->error;
        }
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-2">Daftar Kategori</h2>
                    <p class="card-description">Home / <code>Daftar Kategori</code></p>
                    <div class="d-flex justify-content-end mb-3">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) : ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="bi bi-plus-circle"></i> Tambah Kategori
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table id="kategoriTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <?php
                                    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                                    ?>
                                        <th>Actions</th>
                                    <?php
                                    endif
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = $result->fetch_assoc()) : ?>
                                    <tr class="align-middle">
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['nama_kategori']; ?></td>
                                        <td><?= $row['deskripsi']; ?></td>
                                        <?php
                                        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                                        ?>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editKategori(<?= $row['id_kategori'] ?>, '<?= $row['nama_kategori'] ?>', '<?= $row['deskripsi'] ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteKategori(<?= $row['id_kategori'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        <?php
                                        endif
                                        ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($success_message)) : ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: '<?= $success_message; ?>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php?page=kategori';
                                }
                            });
                        </script>
                    <?php endif; ?>

                    <?php if (!empty($error_message)) : ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: '<?= $error_message; ?>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'index.php?page=kategori';
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </main>

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm" action="" method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" action="" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_kategori" id="edit_id_kategori">
                        <div class="mb-3">
                            <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Kategori -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Hapus Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus kategori ini?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteCategoryForm" action="" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteCategoryId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->


    <script>
        $(document).ready(function() {
            $('#kategoriTable').DataTable();
        });

        function editKategori(id, nama_kategori, deskripsi) {
            $('#edit_id_kategori').val(id);
            $('#edit_nama_kategori').val(nama_kategori);
            $('#edit_deskripsi').val(deskripsi);
            $('#editCategoryModal').modal('show');
        }

        function deleteKategori(id) {
            $('#deleteCategoryId').val(id);
            $('#deleteCategoryModal').modal('show');
        }
    </script>