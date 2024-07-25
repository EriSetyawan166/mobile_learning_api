<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Query untuk mengambil semua data materi
$query = "SELECT id, judul, sub_judul, deskripsi, pengantar, file_path FROM materi";

$result = $conn->query($query);

// Mengecek jika ada data yang ditemukan
if ($result->num_rows > 0) {
    $materi = [];
    while($row = $result->fetch_assoc()) {
        $materi[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $materi]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada materi yang ditemukan']);
}

$conn->close();
?>