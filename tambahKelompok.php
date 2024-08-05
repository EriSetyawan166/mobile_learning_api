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
    if (empty($data['nama'])) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kelompok is required']);
        exit;
    }

    // Sanitasi dan pengambilan data dari JSON
    $nama = $conn->real_escape_string($data['nama']);

    $conn->begin_transaction();
    try {
        // Menambahkan kelompok baru
        $stmt = $conn->prepare("INSERT INTO kelompok (nama) VALUES (?)");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Group added successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>