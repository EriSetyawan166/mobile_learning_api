<?php
require 'conn.php'; // Memanggil file koneksi database

// Menetapkan header content-type
header('Content-Type: application/json');

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        exit;
    }

    $userId = intval($_POST['user_id']);

    // Menyiapkan query untuk menghapus user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);

    // Eksekusi query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found or already deleted']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>