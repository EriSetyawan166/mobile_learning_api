<?php
require 'conn.php'; // Import file koneksi database

header('Content-Type: application/json');

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty($_POST['judul']) || empty($_POST['sub_judul']) || empty($_POST['deskripsi'])) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Sanitasi input
    $judul = $conn->real_escape_string($_POST['judul']);
    $sub_judul = $conn->real_escape_string($_POST['sub_judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

    // Memasukkan data ke dalam database
    try {
        $stmt = $conn->prepare("INSERT INTO kelas (judul, sub_judul, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $judul, $sub_judul, $deskripsi);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kelas']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>