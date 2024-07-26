<?php
require 'conn.php'; // Mengimpor koneksi database

header('Content-Type: application/json');

// Menangani request POST untuk keamanan, gantilah dengan DELETE sesuai preferensi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah ID disediakan
    if (empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID kelas diperlukan']);
        exit;
    }

    // Membersihkan dan memvalidasi ID
    $id = $conn->real_escape_string($_POST['id']);

    // Menghapus kelas dari database
    try {
        $stmt = $conn->prepare("DELETE FROM kelas WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada kelas yang ditemukan dengan ID tersebut']);
            }
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