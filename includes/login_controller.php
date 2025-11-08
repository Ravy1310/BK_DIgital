<?php
// File: login_controller.php

// Jangan echo apapun sebelum JSON
header('Content-Type: application/json');

// TAMBAHKAN SESSION
session_start();

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

    // QUERY YANG DIPERBAIKI - JOIN antara user dan admin
    $sql = "SELECT u.id, u.username, u.password, u.email, u.role, 
                   a.id_admin, a.nama, a.no_telp 
            FROM users u 
            LEFT JOIN admin a ON u.id = a.id_admin 
            WHERE u.username = :username AND u.role IN ('superadmin', 'admin')";
    
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
            
            // SET SESSION SETELAH LOGIN BERHASIL
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_name'] = $row['nama'] ?? $row['username']; // Gunakan nama dari admin atau username
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_role'] = $row['role'];
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            echo json_encode([
                "status" => "success",
                "message" => "Login berhasil.",
                "user" => $row['username'],
                "role" => $row['role'],
                "nama" => $row['nama'] ?? $row['username']
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