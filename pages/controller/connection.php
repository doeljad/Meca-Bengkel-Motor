<?php
// Buat koneksi ke database MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "meca";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
