<?php
include('../../controller/connection.php');

if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Ensure ID is an integer
    $sql = "SELECT * FROM users WHERE id_user=$id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
}
