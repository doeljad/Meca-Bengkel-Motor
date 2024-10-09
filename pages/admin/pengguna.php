<?php
include('pages/controller/connection.php');

$success_message = ""; // Variabel untuk menyimpan pesan sukses
$error_message = "";   // Variabel untuk menyimpan pesan error

// Query untuk mengambil data Pengguna
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Handle POST requests for delete, update, and add
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            // Query untuk menghapus pengguna
            $sql_delete_user = "DELETE FROM users WHERE id_user=$id";

            if ($conn->query($sql_delete_user) === TRUE) {
                $success_message = "Data pengguna berhasil dihapus!";
            } else {
                $error_message = "Error: " . $sql_delete_user . "<br>" . $conn->error;
            }
        } elseif ($_POST['action'] == 'update') {
            $id_user = $_POST['id_user'];
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
            $role = $_POST['role'];

            // Update user data
            $sql_update_user = "UPDATE users SET name='$name', username='$username'" . ($password ? ", password='$password'" : "") . ", role=$role WHERE id_user=$id_user";

            if ($conn->query($sql_update_user) === TRUE) {
                $success_message = "Data pengguna berhasil diupdate!";
            } else {
                $error_message = "Error: " . $sql_update_user . "<br>" . $conn->error;
            }
        } elseif ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];

            // Query untuk menambahkan pengguna
            $sql_add_user = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', $role)";

            if ($conn->query($sql_add_user) === TRUE) {
                $success_message = "Pengguna berhasil ditambahkan!";
            } else {
                $error_message = "Error: " . $sql_add_user . "<br>" . $conn->error;
            }
        }
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-2">Daftar Pengguna</h2>
                    <p class="card-description">Home / <code>Daftar Pengguna</code></p>
                    <div class="d-flex justify-content-end mb-3">

                        <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                        ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPenggunaModal">
                                <i class="bi bi-plus-circle"></i> Tambah Pengguna
                            </button>
                            <!-- <a href="?page=tambah-pesanan" class="btn btn-success"> Tambah Pesanan</a> -->
                        <?php
                        endif
                        ?>
                    </div>
                    <!-- Form Tambah Pengguna -->

                    <div class="table-responsive">
                        <table id="PenggunaTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pengguna</th>
                                    <th>Username</th>
                                    <th>Role</th>
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
                                        <td><?= $row['name']; ?></td>
                                        <td><?= $row['username']; ?></td>
                                        <td>
                                            <?= $row['role'] == 1 ? 'Admin' : ($row['role'] == 2 ? 'Manager' : ($row['role'] == 3 ? 'Pelanggan' : 'Unknown')) ?>
                                        </td>

                                        <?php
                                        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                                        ?>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-btn" data-id="<?= $row['id_user'] ?>">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deletePengguna(<?= $row['id_user'] ?>)">
                                                    <i class="bi bi-trash"></i> Hapus
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

                    <?php if ($success_message) : ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: '<?= $success_message; ?>'
                            }).then(() => {
                                window.location.href = "index.php?page=pengguna";
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
    </main>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addPenggunaModal" tabindex="-1" role="dialog" aria-labelledby="addPenggunaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPenggunaModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPenggunaForm" action="" method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="addName" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="addUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="addRole" class="form-label">Role</label>
                            <select class="form-select" id="addRole" name="role" required>
                                <option value="1">Admin</option>
                                <option value="2">Manager</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div class="modal fade" id="editPenggunaModal" tabindex="-1" role="dialog" aria-labelledby="editPenggunaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPenggunaModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPenggunaForm" action="" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_user" id="editPenggunaId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="1">Admin</option>
                                <option value="2">Manager</option>
                                <option value="3">Pelanggan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Pengguna -->
    <div class="modal fade" id="deletePenggunaModal" tabindex="-1" role="dialog" aria-labelledby="deletePenggunaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePenggunaModalLabel">Hapus Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus Pengguna ini?</p>
                </div>
                <div class="modal-footer">
                    <form id="deletePenggunaForm" action="" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deletePenggunaId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#PenggunaTable').DataTable();

            function editPengguna(id) {
                $.ajax({
                    url: 'pages/admin/controller/get_users.php', // URL untuk mendapatkan data pengguna
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#editPenggunaId').val(data.id_user);
                        $('#editName').val(data.name);
                        $('#editUsername').val(data.username);
                        $('#editRole').val(data.role);
                        $('#editPenggunaModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching user data:', error);
                    }
                });
            }

            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                editPengguna(id);
            });

            window.deletePengguna = function(id) {
                $('#deletePenggunaId').val(id);
                $('#deletePenggunaModal').modal('show');
            }
        });
    </script>