<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Hanya menerima metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input dasar
    if (empty($_POST['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        exit;
    }

    // Sanitasi dan pengambilan data dari POST
    $userId = $conn->real_escape_string($_POST['user_id']);
    $nama = isset($_POST['nama']) ? $conn->real_escape_string($_POST['nama']) : null;
    $nipNis = isset($_POST['nip_nis']) ? $conn->real_escape_string($_POST['nip_nis']) : null;

    // Periksa role user untuk menentukan operasi update
    $roleQuery = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $roleQuery->bind_param("i", $userId);
    $roleQuery->execute();
    $result = $roleQuery->get_result();
    $user = $result->fetch_assoc();
    $role = $user['role'];

    $conn->begin_transaction();
    try {
        // Update data spesifik berdasarkan role
        if ($role === 'guru') {
            $stmt = $conn->prepare("UPDATE guru SET nama_lengkap = ?, nip = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $nama, $nipNis, $userId);
        } elseif ($role === 'siswa') {
            $stmt = $conn->prepare("UPDATE siswa SET nama_lengkap = ?, nis = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $nama, $nipNis, $userId);
        }

        if (isset($stmt)) {
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>