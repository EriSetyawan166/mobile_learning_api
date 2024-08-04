<?php
require 'conn.php'; // Memanggil file koneksi database

// Fetch kelompok
$query = "SELECT id, nama FROM kelompok";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $kelompok = [];
    while($row = $result->fetch_assoc()) {
        $kelompok[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $kelompok]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada kelompok yang ditemukan']);
}

$conn->close();

?>