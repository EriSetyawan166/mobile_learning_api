<?php
require 'conn.php';
// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['role']) || empty($_POST['nama_lengkap']) || empty($_POST['nip_nis'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);
    $namaLengkap = $conn->real_escape_string($_POST['nama_lengkap']);
    $nipNis = $conn->real_escape_string($_POST['nip_nis']);

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $userId = $stmt->insert_id;

        if ($role == 'guru') {
            $stmt = $conn->prepare("INSERT INTO guru (nip, nama_lengkap, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $nipNis, $namaLengkap, $userId);
        } elseif ($role == 'siswa') {
            $stmt = $conn->prepare("INSERT INTO siswa (nis, nama_lengkap, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $nipNis, $namaLengkap, $userId);
        }
        $stmt->execute();
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'User berhasil ditambahkan']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>