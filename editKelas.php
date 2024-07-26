<?php
require 'conn.php'; // Mengimpor koneksi database

header('Content-Type: application/json');

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah semua field yang diperlukan ada
    if (empty($_POST['id']) || empty($_POST['judul']) || empty($_POST['deskripsi'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Membersihkan dan memvalidasi input
    $id = $conn->real_escape_string($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

    // Memperbarui database
    try {
        $stmt = $conn->prepare("UPDATE kelas SET judul=?, deskripsi=? WHERE id=?");
        $stmt->bind_param("ssi", $judul, $deskripsi, $id);
        
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