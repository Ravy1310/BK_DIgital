<?php
// Perhatikan: Pastikan file db_connection.php mengembalikan objek $conn
include 'db_connection.php';

// Jangan echo apapun sebelum JSON
header('Content-Type: application/json');

// Cek koneksi dari db_connection.php
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal koneksi ke database."
    ]);
    exit;
}

// Ambil data dari POST dan bersihkan
// Menggunakan isset() untuk memastikan variabel ada
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Data input tidak lengkap."
    ]);
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

// Cek di database menggunakan PREPARED STATEMENTS (Solusi untuk SQL Injection)
$sql = "SELECT username, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Kesalahan jika prepared statement gagal
    echo json_encode([
        "status" => "error",
        "message" => "Query persiapan gagal: " . $conn->error
    ]);
    exit;
}

// 's' menandakan bahwa variabel adalah string
$stmt->bind_param("s", $username); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
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
        // agar tidak memberikan petunjuk ke penyerang (keamanan)
        echo json_encode([
            "status" => "error",
            "message" => "Username atau Password salah."
        ]);
    }
} else {
    // User tidak ditemukan, memberikan pesan error yang sama
    echo json_encode([
        "status" => "error",
        "message" => "Username atau Password salah."
    ]);
}

$stmt->close();
$conn->close();
?>