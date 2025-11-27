<?php
// File: login_controller.php

header('Content-Type: application/json');
session_start();
require_once 'db_connection.php';

try {
    if (!$pdo) {
        throw new Exception('Database connection not available');
    }

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        throw new Exception('Data input tidak lengkap.');
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // QUERY DIPERBAIKI - Gunakan LEFT JOIN dan handle NULL
    $sql = "SELECT u.id, u.username, u.password, u.email, u.role, 
                   a.id_admin, a.nama as nama_admin, a.no_telp,
                   g.id_guru, g.nama as nama_guru, g.status as status_guru
            FROM users u 
            LEFT JOIN admin a ON u.id = a.id_admin 
            LEFT JOIN guru g ON u.id_guru = g.id_guru
            WHERE u.username = :username AND u.role IN ('superadmin', 'admin', 'user')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            
            // CEK JIKA ROLE USER (GURU)
            if ($row['role'] === 'user') {
                // Jika data guru tidak ditemukan (NULL)
                if ($row['id_guru'] === null || $row['status_guru'] === null) {
                    throw new Exception('Akun guru belum terdaftar dengan lengkap. Silakan hubungi administrator.');
                }
                
                // CEK STATUS GURU
                if ($row['status_guru'] !== 'Aktif') {
                    throw new Exception('Akun guru tidak aktif. Silakan hubungi administrator.');
                }
                
                // Gunakan nama_guru jika ada,否则gunakan username
                $nama = !empty($row['nama_guru']) ? $row['nama_guru'] : $row['username'];
            } else {
                // Untuk admin/superadmin, gunakan nama_admin atau username
                $nama = !empty($row['nama_admin']) ? $row['nama_admin'] : $row['username'];
            }
            
            // SET SESSION
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_name'] = $nama;
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_role'] = $row['role'];
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Tambahkan session khusus untuk guru
            if ($row['role'] === 'user' && $row['id_guru'] !== null) {
                $_SESSION['guru_id'] = $row['id_guru'];
                $_SESSION['guru_status'] = $row['status_guru'];
                $_SESSION['is_guru'] = true;
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Login berhasil.",
                "user" => $row['username'],
                "role" => $row['role'],
                "nama" => $nama
            ]);
            
        } else {
            throw new Exception('Username atau Password salah.');
        }
    } else {
        throw new Exception('Username atau Password salah.');
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

exit;
?>