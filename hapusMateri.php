<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['materi_id']) || empty($_POST['materi_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Materi ID is required']);
        exit;
    }

    $materiId = intval($_POST['materi_id']);

    // Cek apakah file terkait dengan materi tersebut perlu dihapus juga
    $checkQuery = $conn->prepare("SELECT file_path FROM materi WHERE id = ?");
    $checkQuery->bind_param("i", $materiId);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    $file = $result->fetch_assoc();
    
    // Jalur lengkap ke file dalam folder 'uploads'
    $filePath = __DIR__ . '/uploads/' . basename($file['file_path']);

    // Menghapus file dari server jika ada
    if ($file && file_exists($filePath)) {
        if (!unlink($filePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete the file associated with the materi']);
            exit;
        }
    }

    // Menyiapkan query untuk menghapus materi
    $stmt = $conn->prepare("DELETE FROM materi WHERE id = ?");
    $stmt->bind_param("i", $materiId);

    // Eksekusi query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Materi deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Materi not found or already deleted']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete materi']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>