<?php
// File: includes/db_connection.php

$host = 'localhost';
$username = 'u804549048_Kelompok_4';
$password = 'Kelompok4$';
$dbname = 'u804549048_bk_digital';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Test koneksi
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Jika koneksi gagal, set $pdo ke null (akan menggunakan session storage)
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}

// $pdo akan tersedia untuk file lain yang require file ini
?>