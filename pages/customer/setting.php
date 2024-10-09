<?php

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Menyertakan koneksi database
include('pages/controller/connection.php');

// Variabel untuk menyimpan pesan error atau sukses
$error_message = '';
$success_message = '';

// Mendapatkan informasi pengguna dari sesi
$id_user = $_SESSION['id_user'];

// Memproses data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];

    // Memeriksa apakah password lama sesuai dengan yang ada di database
    $sql = "SELECT password FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    if ($password === $db_password) {
        // Jika pengguna ingin mengganti password
        if (!empty($new_password)) {
            $sql = "UPDATE users SET name = ?, username = ?, password = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $username, $new_password, $id_user);
        } else {
            // Jika pengguna tidak ingin mengganti password
            $sql = "UPDATE users SET name = ?, username = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $username, $id_user);
        }

        if ($stmt->execute()) {
            // Memperbarui data pelanggan
            $sql = "UPDATE pelanggan SET nama = ?, email = ?, no_telp = ?, alamat = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $no_telp, $alamat, $id_user);

            if ($stmt->execute()) {
                $success_message = "Profil berhasil diperbarui!";
                $_SESSION['name'] = $name;
            } else {
                $error_message = "Terjadi kesalahan saat memperbarui profil pelanggan.";
            }
        } else {
            $error_message = "Terjadi kesalahan saat memperbarui profil pengguna.";
        }

        $stmt->close();
    } else {
        $error_message = "Password lama tidak sesuai!";
    }
}

// Mendapatkan informasi pengguna saat ini
$sql = "SELECT u.name, u.username, p.email, p.no_telp, p.alamat FROM users u JOIN pelanggan p ON u.id_user = p.id_user WHERE u.id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($name, $username, $email, $no_telp, $alamat);
$stmt->fetch();
$stmt->close();
?>
<form action="" method="post" class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header text-center">
            <h3>Pengaturan Profil</h3>
        </div>
        <div class="card-body">
            <?php if ($error_message) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message) : ?>
                <div class="alert alert-success text-center" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="mb-3">
                <label for="no_telp" class="form-label">No. Telepon</label>
                <input type="text" class="form-control" id="no_telp" name="no_telp" placeholder="No. Telepon" value="<?php echo htmlspecialchars($no_telp); ?>" required>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" value="<?php echo htmlspecialchars($alamat); ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password Lama</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password Lama" required>
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Password Baru">
                <p>Biarkan kosong jika tidak ingin mengganti password</p>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</form>