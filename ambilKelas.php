<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Menyimpan kelompok dari parameter GET atau POST (ubah sesuai kebutuhan)
$kelompok = isset($_GET['kelompok']) ? $_GET['kelompok'] : '';

if (!$kelompok) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter kelompok diperlukan']);
    exit;
}

// Query untuk mengambil semua data materi berdasarkan kelompok
$query = "SELECT id, judul, sub_judul, deskripsi FROM kelas WHERE kelompok = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $kelompok); // 's' menunjukkan tipe data string
$stmt->execute();
$result = $stmt->get_result();

// Mengecek jika ada data yang ditemukan
if ($result->num_rows > 0) {
    $kelas = [];
    while($row = $result->fetch_assoc()) {
        $kelas[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $kelas]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada materi yang ditemukan untuk kelompok ini']);
}

$stmt->close();
$conn->close();
?>