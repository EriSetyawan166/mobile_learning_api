<?php
require 'conn.php';  // Assuming conn.php sets up your database connection

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit;
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role, kelompok FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'role' => $user['role'],
                'user_id' => $user['id'],
                'kelompok' => $user['kelompok'] 
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User does not exist']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

?>