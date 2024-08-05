<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil input mentah
    $input = file_get_contents('php://input');
    // Mengurai JSON input
    $data = json_decode($input, true);

    // Log untuk debugging
    file_put_contents('php://stderr', print_r($data, TRUE), FILE_APPEND);

    // Validasi input dasar
    if (empty($data['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        exit;
    }

    // Sanitasi dan pengambilan data dari JSON
    $userId = $conn->real_escape_string($data['user_id']);

    $conn->begin_transaction();
    try {
        // Menghapus kelompok berdasarkan ID
        $stmt = $conn->prepare("DELETE FROM kelompok WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Group deleted successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>