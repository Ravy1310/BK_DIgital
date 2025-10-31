<?php
// File: login_controller.php

// Jangan echo apapun sebelum JSON
header('Content-Type: application/json');

// Include koneksi PDO
require_once 'db_connection.php';

try {
    // Cek koneksi PDO
    if (!$pdo) {
        throw new Exception('Database connection not available');
    }

    // Ambil data dari POST dan bersihkan
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        throw new Exception('Data input tidak lengkap.');
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di database menggunakan PREPARED STATEMENTS PDO
    $sql = "SELECT username, password, role FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);

    if ($stmt === false) {
        throw new Exception('Query persiapan gagal.');
    }

    // Bind parameter dan execute
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    // Cek hasil
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifikasi password yang sudah di-hash
        if (password_verify($password, $row['password'])) {
            echo json_encode([
                "status" => "success",
                "message" => "Login berhasil.",
                "user" => $row['username'],
                "role" => $row['role']
            ]);
        } else {
            // Pesan error yang sama untuk user tidak ditemukan dan password salah
            throw new Exception('Username atau Password salah.');
        }
    } else {
        // User tidak ditemukan
        throw new Exception('Username atau Password salah.');
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

// PDO tidak perlu manual close connection
exit;
?>