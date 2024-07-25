<?php
require 'conn.php'; // Import the database connection file

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present
    if (empty($_POST['id']) || empty($_POST['judul']) || empty($_POST['sub_judul']) || empty($_POST['deskripsi']) || empty($_POST['pengantar'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Sanitize and validate input
    $id = $conn->real_escape_string($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $subJudul = $conn->real_escape_string($_POST['sub_judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $pengantar = $conn->real_escape_string($_POST['pengantar']);

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
        $uploadPath = $_POST['existing_file_path'];
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE materi SET judul=?, sub_judul=?, deskripsi=?, pengantar=?, file_path=? WHERE id=?");
    $stmt->bind_param("sssssi", $judul, $subJudul, $deskripsi, $pengantar, $uploadPath, $id);
    
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