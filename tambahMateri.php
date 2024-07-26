<?php
require 'conn.php'; // Mengimpor file koneksi database

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input teks dan kelas_id
    if (empty($_POST['judul']) || empty($_POST['deskripsi']) || empty($_FILES['filepdf']['name']) || empty($_POST['kelas_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Sanitasi input teks
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kelas_id = $conn->real_escape_string($_POST['kelas_id']);

    // Handle file PDF
    $filePdf = $_FILES['filepdf'];
    $fileTmpPath = $filePdf['tmp_name'];
    $fileSize = $filePdf['size'];
    $fileType = $filePdf['type'];

    // Mengganti spasi dengan underscore dan menambahkan waktu unik untuk menghindari overwrite
    $newFileName = strtolower(str_replace(" ", "_", $judul)) . "_" . time() . ".pdf";
    $uploadPath = "uploads/" . $newFileName;

    // Pastikan file yang diunggah adalah PDF
    if(strtolower(pathinfo($filePdf['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        echo json_encode(['status' => 'error', 'message' => 'Hanya file PDF yang diizinkan']);
        exit;
    }

    // Pindahkan file ke lokasi yang diinginkan
    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
        try {
            // Insert data ke tabel materi
            $stmt = $conn->prepare("INSERT INTO materi (judul, deskripsi, file_path, kelas_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $judul, $deskripsi, $uploadPath, $kelas_id);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Materi berhasil ditambahkan']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah file']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>