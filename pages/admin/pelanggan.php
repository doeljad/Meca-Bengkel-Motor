<?php
include('pages/controller/connection.php');

$success_message = ""; // Variabel untuk menyimpan pesan sukses
$error_message = "";   // Variabel untuk menyimpan pesan error

// Query untuk mengambil data pelanggan
$sql = "SELECT c.*, u.name AS user_name, u.username, u.role
        FROM pelanggan c
        INNER JOIN users u ON c.id_user = u.id_user";

$result = $conn->query($sql);

// Handle POST requests for delete, add, and update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete') {
            $id = $_POST['id'];

            // Query untuk menghapus pelanggan
            $sql_delete_customer = "DELETE FROM pelanggan WHERE id_pelanggan=$id";

            if ($conn->query($sql_delete_customer) === TRUE) {
                $success_message = "Data pelanggan berhasil dihapus!";
            } else {
                $error_message = "Error: " . $sql_delete_customer . "<br>" . $conn->error;
            }
        } elseif ($_POST['action'] == 'add') {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            $email = $_POST['email'];
            $no_telp = $_POST['no_telp'];
            $alamat = $_POST['alamat'];

            // Insert user and customer data
            $sql_add_user = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', $role)";
            if ($conn->query($sql_add_user) === TRUE) {
                $id_user = $conn->insert_id;
                $sql_add_customer = "INSERT INTO pelanggan (id_user, nama, email, no_telp, alamat) VALUES ($id_user, '$name', '$email', '$no_telp', '$alamat')";

                if ($conn->query($sql_add_customer) === TRUE) {
                    $success_message = "Data pelanggan berhasil ditambahkan!";
                } else {
                    $error_message = "Error: " . $sql_add_customer . "<br>" . $conn->error;
                }
            } else {
                $error_message = "Error: " . $sql_add_user . "<br>" . $conn->error;
            }
        } elseif ($_POST['action'] == 'update') {
            $id_pelanggan = $_POST['id_pelanggan'];
            $name = $_POST['name'];
            $username = $_POST['username'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            // $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
            $role = $_POST['role'];
            $email = $_POST['email'];
            $no_telp = $_POST['no_telp'];
            $alamat = $_POST['alamat'];

            // Update user and customer data
            $sql_update_customer = "UPDATE pelanggan SET nama='$name', email='$email', no_telp='$no_telp', alamat='$alamat' WHERE id_pelanggan=$id_pelanggan";
            if ($conn->query($sql_update_customer) === TRUE) {
                $sql_update_user = "UPDATE users SET name='$name', username='$username'" . ($password ? ", password='$password'" : "") . ", role=$role WHERE id_user=(SELECT id_user FROM pelanggan WHERE id_pelanggan=$id_pelanggan)";

                if ($conn->query($sql_update_user) === TRUE) {
                    $success_message = "Data pelanggan berhasil diupdate!";
                } else {
                    $error_message = "Error: " . $sql_update_user . "<br>" . $conn->error;
                }
            } else {
                $error_message = "Error: " . $sql_update_customer . "<br>" . $conn->error;
            }
        }
    }
}
?>

<?php if ($success_message) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= $success_message; ?>',
            didClose: () => {
                window.location.href = 'index.php?page=pelanggan';
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
        });
    </script>
<?php endif; ?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-2">Daftar Pelanggan</h2>
                    <p class="card-description">Home / <code>Daftar Pelanggan</code></p>
                    <div class="d-flex justify-content-end mb-3">
                        <!-- <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1) : ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                <i class="bi bi-plus-circle"></i> Tambah Pelanggan
                            </button>
                        <?php endif; ?> -->
                    </div>
                    <div class="table-responsive">
                        <table id="pelangganTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th>Username</th>
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
                                        <td><?= $row['nama']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td><?= $row['no_telp']; ?></td>
                                        <td style="max-width: 150px; word-wrap: break-word; white-space: normal;">
                                            <?php
                                            $words = explode(' ', $row['alamat']);
                                            $maxWords = 20;

                                            if (count($words) > $maxWords) {
                                                $displayText = implode(' ', array_slice($words, 0, $maxWords)) . '<br>' . implode(' ', array_slice($words, $maxWords));
                                            } else {
                                                $displayText = $row['alamat'];
                                            }

                                            echo nl2br($displayText);
                                            ?>
                                        </td>
                                        <td><?= $row['username']; ?></td>
                                        <?php
                                        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) :
                                        ?>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick='updatePelanggan(<?= json_encode($row) ?>)'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deletePelanggan(<?= $row['id_pelanggan'] ?>)">
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
                </div>
            </div>
        </div>
    </div>
    </main>

    <!-- Modal Hapus Pelanggan -->
    <div class="modal fade" id="deletePelangganModal" tabindex="-1" role="dialog" aria-labelledby="deletePelangganModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePelangganModalLabel">Hapus Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus pelanggan ini?</p>
                </div>
                <div class="modal-footer">
                    <form id="deletePelangganForm" action="" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deletePelangganId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pelanggan -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm" action="" method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                                <option value="3">Pelanggan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pelanggan -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm" action="" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_pelanggan" id="editCustomerId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                                <option value="3">Pelanggan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNoTelp" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="editNoTelp" name="no_telp">
                        </div>
                        <div class="mb-3">
                            <label for="editAlamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="editAlamat" name="alamat"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#pelangganTable').DataTable();

            window.updatePelanggan = function(customer) {
                $('#editCustomerId').val(customer.id_pelanggan);
                $('#editName').val(customer.nama);
                $('#editUsername').val(customer.username);
                $('#editEmail').val(customer.email);
                $('#editNoTelp').val(customer.no_telp);
                $('#editAlamat').val(customer.alamat);
                $('#editRole').val(customer.role);
                $('#editCustomerModal').modal('show');
            };

            window.deletePelanggan = function(id) {
                $('#deletePelangganId').val(id);
                $('#deletePelangganModal').modal('show');
            };
        });
    </script>