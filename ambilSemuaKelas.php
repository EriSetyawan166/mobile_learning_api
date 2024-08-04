<?php
require 'conn.php'; // File koneksi ke database

header('Content-Type: application/json');

$query = "SELECT id, judul, sub_judul, deskripsi FROM kelas"; // Query untuk mengambil semua kelas

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $kelas = [];
    while($row = $result->fetch_assoc()) {
        $kelas[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $kelas]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada kelas yang ditemukan']);
}

$conn->close();
?>