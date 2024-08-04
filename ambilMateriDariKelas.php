<?php
require 'conn.php'; // Include the database connection file

header('Content-Type: application/json'); // Set the content type of the response to JSON

// Retrieve kelas_id from the GET request
$kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;

// Prepare a SQL query to fetch all materials for a specific class
$query = "SELECT id, judul, deskripsi, file_path FROM materi WHERE kelas_id = ?";

// Prepare the query statement
$stmt = $conn->prepare($query);

// Check if the statement was prepared correctly
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the query']);
    $conn->close();
    exit;
}

// Bind the kelas_id parameter to the query
$stmt->bind_param("i", $kelas_id);

// Execute the query
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if there are any materials found
if ($result->num_rows > 0) {
    $kelas = [];
    while ($row = $result->fetch_assoc()) {
        $kelas[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $kelas]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Materi Tidak Ditemukan']);
}

// Close the statement and the connection
$stmt->close();
$conn->close();
?>