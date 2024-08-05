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
    $nama = isset($data['nama']) ? $conn->real_escape_string($data['nama']) : null;
    $username = isset($data['username']) ? $conn->real_escape_string($data['username']) : null;
    $nipNis = isset($data['nip_nis']) ? $conn->real_escape_string($data['nip_nis']) : null;
    $kelompokId = isset($data['kelompok_id']) ? $conn->real_escape_string($data['kelompok_id']) : null;

    // Log untuk debugging
    file_put_contents('php://stderr', "User ID: $userId, Nama: $nama, Username: $username, NIP/NIS: $nipNis, Kelompok ID: $kelompokId\n", FILE_APPEND);

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

        // Update username dan kelompok di tabel users
        $stmt = $conn->prepare("UPDATE users SET username = ?, kelompok = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $kelompokId, $userId);
        $stmt->execute();
        $stmt->close();

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