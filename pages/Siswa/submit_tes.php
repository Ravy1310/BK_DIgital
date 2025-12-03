<?php
session_start();

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ HAPUS LOGGING KE FILE:
// file_put_contents('submit_debug.log', "\n=== " . date('Y-m-d H:i:s') . " ==========\n", FILE_APPEND);
// file_put_contents('submit_debug.log', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);
// file_put_contents('submit_debug.log', "SESSION Data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Validasi session
require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";

if (!isVerifiedFor('tes')) {
    $_SESSION['error'] = "Session tidak valid. Silakan login ulang.";
    header("Location: verifikasi_tes.php");
    exit;
}

// Dapatkan data siswa
$siswa_data = getCurrentStudent();
if (!$siswa_data || !isset($siswa_data['id_siswa'])) {
    $_SESSION['error'] = "Data siswa tidak ditemukan.";
    header("Location: verifikasi_tes.php");
    exit;
}

$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];

// ✅ HAPUS:
// file_put_contents('submit_debug.log', "Siswa: $id_siswa - $nama_siswa\n", FILE_APPEND);

// Include controller
require_once __DIR__ . "/../../includes/db_connection.php";
require_once __DIR__ . "/../../includes/siswa_control/tes_controller.php";

// Ambil data dari POST
$id_tes = isset($_POST['id_tes']) ? (int)$_POST['id_tes'] : 0;
$jawaban = $_POST['jawaban'] ?? [];

// ✅ HAPUS:
// file_put_contents('submit_debug.log', "ID Tes: $id_tes, Jawaban count: " . count($jawaban) . "\n", FILE_APPEND);

// Validasi input
if ($id_tes <= 0) {
    $_SESSION['error'] = "ID Tes tidak valid";
    header("Location: tesbk.php");
    exit;
}

if (empty($jawaban)) {
    $_SESSION['error'] = "Tidak ada jawaban yang dikirim. Silakan lengkapi semua soal.";
    header("Location: form_tes.php?id=" . $id_tes);
    exit;
}

// Proses submit tes
$result = submitTes($id_siswa, $id_tes, $jawaban);

// ✅ HAPUS:
// file_put_contents('submit_debug.log', "Submit Result: " . print_r($result, true) . "\n", FILE_APPEND);

// Handle hasil submit
if (isset($result['success']) && $result['success'] === true) {
    // ✅ Coba beberapa metode untuk mendapatkan ID hasil yang benar
    $id_hasil_redirect = 0;
    
    // 1. Gunakan id_hasil dari result jika ada
    if (isset($result['id_hasil']) && $result['id_hasil'] > 0) {
        $id_hasil_redirect = $result['id_hasil'];
        // ✅ HAPUS: file_put_contents('submit_debug.log', "Menggunakan id_hasil dari result: $id_hasil_redirect\n", FILE_APPEND);
    }
    
    // 2. Jika masih 0, cari di database
    if ($id_hasil_redirect <= 0) {
        try {
            $stmt = $pdo->prepare("
                SELECT id_hasil 
                FROM hasil_tes 
                WHERE id_siswa = ? AND id_tes = ? 
                ORDER BY tanggal_submit DESC 
                LIMIT 1
            ");
            $stmt->execute([$id_siswa, $id_tes]);
            $id_hasil_redirect = $stmt->fetchColumn();
            
            // ✅ HAPUS: file_put_contents('submit_debug.log', "Menggunakan id_hash dari database: $id_hasil_redirect\n", FILE_APPEND);
        } catch (Exception $e) {
            // ✅ HAPUS: file_put_contents('submit_debug.log', "Error mencari id_hash: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
    
    // 3. Jika masih 0, cari ID terbaru untuk siswa ini
    if ($id_hasil_redirect <= 0) {
        try {
            $stmt = $pdo->prepare("
                SELECT id_hasil 
                FROM hasil_tes 
                WHERE id_siswa = ? 
                ORDER BY tanggal_submit DESC 
                LIMIT 1
            ");
            $stmt->execute([$id_siswa]);
            $id_hasil_redirect = $stmt->fetchColumn();
            
            // ✅ HAPUS: file_put_contents('submit_debug.log', "Menggunakan id_hash terbaru: $id_hasil_redirect\n", FILE_APPEND);
        } catch (Exception $e) {
            // ✅ HAPUS: file_put_contents('submit_debug.log', "Error mencari id_hash terbaru: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
    
   
    
    // Jika mendapatkan ID, redirect ke halaman hasil
    if ($id_hasil_redirect > 0) {
        // ✅ HAPUS: file_put_contents('submit_debug.log', "Redirect ke: hasil_tes.php?id=$id_hasil_redirect\n", FILE_APPEND);
        header("Location: hasil_tes.php?id=" . $id_hasil_redirect);
        exit;
    } else {
        // Fallback ke tesbk.php
        // ✅ HAPUS: file_put_contents('submit_debug.log', "Tidak dapat menemukan ID hasil, redirect ke tesbk.php\n", FILE_APPEND);
        header("Location: tesbk.php");
        exit;
    }
    
} else {
    // Jika gagal
    $errorMsg = $result['message'] ?? "Terjadi kesalahan yang tidak diketahui";
    $_SESSION['error'] = "Gagal menyimpan tes: " . $errorMsg;
    header("Location: form_tes.php?id=" . $id_tes);
    exit;
}
?>