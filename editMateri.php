<?php
require 'conn.php'; // Import the database connection file

header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present
    if (empty($_POST['id']) || empty($_POST['judul']) || empty($_POST['deskripsi']) || empty($_POST['kelas_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Sanitize and validate input
    $id = $conn->real_escape_string($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kelas_id = $conn->real_escape_string($_POST['kelas_id']);

    // Optional: Handle file updates
    if (!empty($_FILES['filepdf']['name'])) {
        $filePdf = $_FILES['filepdf'];
        $fileTmpPath = $filePdf['tmp_name'];
        $newFileName = strtolower(str_replace(" ", "_", $judul)) . "_" . time() . ".pdf";
        $uploadPath = "uploads/" . $newFileName;

        if (strtolower(pathinfo($filePdf['name'], PATHINFO_EXTENSION)) != 'pdf') {
            echo json_encode(['status' => 'error', 'message' => 'Only PDF files are allowed']);
            exit;
        }

        if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
            exit;
        }
    } else {
        // Assume the existing path remains if no new file is uploaded
        $uploadPath = $_POST['existing_file_path'] ?? null; // Using null coalescing operator for fallback
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE materi SET judul=?, deskripsi=?, file_path=?, kelas_id=? WHERE id=?");
    $stmt->bind_param("ssssi", $judul, $deskripsi, $uploadPath, $kelas_id, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Materi berhasil diupdate']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>