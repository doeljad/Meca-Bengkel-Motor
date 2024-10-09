<?php
session_start();

// Menghancurkan semua sesi
session_destroy();

// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php");
exit();
