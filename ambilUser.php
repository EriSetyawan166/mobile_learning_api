<?php
require 'conn.php'; // Memanggil file koneksi database

header('Content-Type: application/json'); // Set header response ke JSON

// Query untuk mengambil semua pengguna, gabungkan data guru dan siswa
$query = "SELECT u.id, u.username, u.role, g.nip, g.nama_lengkap AS nama, NULL AS nis
          FROM users u 
          LEFT JOIN guru g ON u.id = g.user_id 
          WHERE u.role = 'guru'
          UNION ALL
          SELECT u.id, u.username, u.role, NULL AS nip, s.nama_lengkap AS nama, s.nis
          FROM users u
          LEFT JOIN siswa s ON u.id = s.user_id
          WHERE u.role = 'siswa'";

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
?>