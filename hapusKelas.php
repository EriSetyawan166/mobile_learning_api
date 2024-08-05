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

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Mengambil semua jalur file materi yang terkait dengan kelas
        $materiQuery = $conn->prepare("SELECT file_path FROM materi WHERE kelas_id = ?");
        $materiQuery->bind_param("i", $id);
        $materiQuery->execute();
        $result = $materiQuery->get_result();

        $filePaths = [];
        while ($row = $result->fetch_assoc()) {
            $filePaths[] = $row['file_path'];
        }
        $materiQuery->close();

        // Menghapus file-file tersebut dari server
        foreach ($filePaths as $filePath) {
            $fullPath = __DIR__ . '/uploads/' . basename($filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        // Menghapus data materi dari database
        $deleteMateriQuery = $conn->prepare("DELETE FROM materi WHERE kelas_id = ?");
        $deleteMateriQuery->bind_param("i", $id);
        $deleteMateriQuery->execute();
        $deleteMateriQuery->close();

        // Menghapus kelas dari database
        $deleteKelasQuery = $conn->prepare("DELETE FROM kelas WHERE id = ?");
        $deleteKelasQuery->bind_param("i", $id);
        if ($deleteKelasQuery->execute()) {
            if ($deleteKelasQuery->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Kelas dan semua materinya berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada kelas yang ditemukan dengan ID tersebut']);
            }
        } else {
            throw new Exception('Database error: ' . $deleteKelasQuery->error);
        }
        $deleteKelasQuery->close();

        // Commit transaksi
        $conn->commit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>