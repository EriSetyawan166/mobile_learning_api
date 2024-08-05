<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

try {
    // Query untuk mengambil semua pengguna, gabungkan data guru dan siswa, dan sertakan ID kelompok dan nama kelompok
    $query = "SELECT u.id, u.username, u.role, g.nip, s.nis, 
                     COALESCE(g.nama_lengkap, s.nama_lengkap, u.username) AS nama, 
                     k.id AS kelompok_id, k.nama AS kelompok
              FROM users u
              LEFT JOIN guru g ON u.id = g.user_id 
              LEFT JOIN siswa s ON u.id = s.user_id
              LEFT JOIN kelompok k ON u.kelompok = k.id";

    $result = $conn->query($query);

    // Mengecek jika ada data yang ditemukan
    if ($result->num_rows > 0) {
        $users = [];
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $users]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data pengguna']);
    }

    $conn->close();
} catch (Exception $e) {
    // Mengirimkan pesan kesalahan dalam format JSON
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>