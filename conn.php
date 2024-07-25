<?php
// Koneksi ke database
$host = "localhost"; 
$dbname = "mobile_learning"; 
$username = "root";
$password = "";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>