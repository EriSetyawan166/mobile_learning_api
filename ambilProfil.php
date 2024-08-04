<?php
require 'conn.php';

header('Content-Type: application/json');

// Menerima ID dan Role dari request
$user_id = $_GET['user_id'];
$role = $_GET['role']; // 'siswa' atau 'guru'

if ($role == 'siswa') {
    $query = "SELECT users.username, users.kelompok, siswa.nama_lengkap, siswa.nis 
              FROM users
              JOIN siswa ON users.id = siswa.user_id
              WHERE users.id = ?";
} else {
    $query = "SELECT users.username, users.kelompok, guru.nama_lengkap, guru.nip 
              FROM users
              JOIN guru ON users.id = guru.user_id
              WHERE users.id = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($data = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found']);
}

$stmt->close();
$conn->close();
?>