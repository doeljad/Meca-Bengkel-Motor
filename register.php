<?php
session_start();

// Memeriksa apakah pengguna sudah login, jika iya, arahkan ke halaman utama
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit();
}

// Variabel untuk menyimpan pesan error atau sukses
$error_message = '';
$success_message = '';

// Memproses data saat form register disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('pages/controller/connection.php');

    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $role = 3; // Default role untuk user biasa

    // Memeriksa apakah username sudah ada di database
    $sql = "SELECT id_user FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Username sudah digunakan!";
    } else {
        // Menyimpan data ke tabel users
        $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $username, $password, $role);

        if ($stmt->execute()) {
            $id_user = $stmt->insert_id; // Mengambil ID user yang baru saja didaftarkan

            // Menyimpan data ke tabel pelanggan
            $stmt = $conn->prepare("INSERT INTO pelanggan (id_user, nama, email, no_telp, alamat) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id_user, $name, $email, $no_telp, $alamat);

            if ($stmt->execute()) {
                $success_message = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error_message = "Terjadi kesalahan saat menyimpan data pelanggan.";
            }
        } else {
            $error_message = "Terjadi kesalahan saat menyimpan data pengguna.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Form Pendaftaran</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style media="screen">
        /* Styling untuk form */
        *,
        *:before,
        *:after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #723957;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120vh;
            padding-top: 100px;
            padding-bottom: 100px;
        }

        form {
            width: 400px;
            background-color: rgba(255, 255, 255, 0.13);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 50px 35px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        form * {
            color: #ffffff;
            letter-spacing: 0.5px;
            outline: none;
            border: none;
        }

        form h3 {
            font-size: 32px;
            font-weight: 500;
            line-height: 42px;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-size: 16px;
            font-weight: 500;
            display: block;
            margin-top: 20px;
        }

        input {
            height: 50px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
            padding: 0 10px;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 300;
        }

        ::placeholder {
            color: #e5e5e5;
        }

        button {
            margin-top: 40px;
            width: 100%;
            background-color: #ffffff;
            color: #080710;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #9989f4;
        }

        .login-button {
            display: block;
            text-align: center;
            margin-top: 15px;
            width: 100%;
            background-color: #FFDE59;
            color: #080710;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #DEB40A;
        }
    </style>
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h3>Form Pendaftaran</h3>

        <?php if ($error_message) : ?>
            <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message) : ?>
            <div style="color: white; text-align: center; margin-bottom: 15px;"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <label for="name">Nama</label>
        <input type="text" placeholder="Nama Lengkap" id="name" name="name" required>

        <label for="username">Username</label>
        <input type="text" placeholder="Username" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" placeholder="Password" id="password" name="password" required>

        <label for="email">Email</label>
        <input type="email" placeholder="Email" id="email" name="email" required>

        <label for="no_telp">No. Telepon</label>
        <input type="text" placeholder="No. Telepon" id="no_telp" name="no_telp" required>

        <label for="alamat">Alamat</label>
        <input type="text" placeholder="Alamat" id="alamat" name="alamat" required>

        <button type="submit">Daftar</button>
        <a href="login.php" class="login-button">Login</a>

    </form>

</body>

</html>