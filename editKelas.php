<?php
require 'conn.php'; // Mengimpor koneksi database

header('Content-Type: application/json');

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah semua field yang diperlukan ada
    if (empty($_POST['id']) || empty($_POST['judul']) || empty($_POST['sub_judul']) || empty($_POST['deskripsi']) || empty($_POST['kelompok'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Membersihkan dan memvalidasi input
    $id = $conn->real_escape_string($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $sub_judul = $conn->real_escape_string($_POST['sub_judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kelompok = $conn->real_escape_string($_POST['kelompok']);

    // Memperbarui database
    try {
        $stmt = $conn->prepare("UPDATE kelas SET judul=?, sub_judul=?, deskripsi=?, kelompok=? WHERE id=?");
        $stmt->bind_param("ssssi", $judul, $sub_judul, $deskripsi, $kelompok, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>