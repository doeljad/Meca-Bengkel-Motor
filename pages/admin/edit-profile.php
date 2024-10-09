<?php
// session_start();
include('pages/controller/connection.php');

if (!isset($_SESSION['id_user'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($password)) {
        // $password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET name = ?, username = ?, password = ? WHERE id_user = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $username, $password, $id_user);
    } else {
        $update_sql = "UPDATE users SET name = ?, username = ? WHERE id_user = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $name, $username, $id_user);
    }

    if ($update_stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Profile updated successfully!',
            }).then(function() {
                window.location = 'index.php?page=edit-profile';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error updating profile: " . $conn->error . "',
            });
        </script>";
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h2 class="mb-2">Edit Profile</h2>
                    <p class="card-description">Home / <code>Edit Profile</code></p>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Biarkan kosong jika tidak mengganti password</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </main>