<?php
session_start();
require_once __DIR__ . "/../../includes/db_connection.php";

// Pastikan form dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: buat_pengaduan.php");
    exit;
}

// Ambil data form
$jenis_laporan = $_POST['jenis_laporan'] ?? '';
$jenis_kejadian = $_POST['jenis_kejadian'] ?? '';
$penjelasan = $_POST['penjelasan'] ?? '';

// Tentukan id_siswa sesuai jenis laporan
if ($jenis_laporan === 'Anonim') {
    $id_siswa = null; // disembunyikan / anonim
} else {
    // Harus sudah verifikasi NIS
    if (!isset($_SESSION['siswa_logged_in'])) {
        header("Location: verifikasi_id.php?error=need_verify");
        exit;
    }
    $id_siswa = $_SESSION['siswa_id'];
}

// Query INSERT
$stmt = $pdo->prepare("
    INSERT INTO pengaduan (id_siswa, jenis_laporan, jenis_kejadian, penjelasan, tanggal)
    VALUES (?, ?, ?, ?, NOW())
");

$success = $stmt->execute([$id_siswa, $jenis_laporan, $jenis_kejadian, $penjelasan]);

// Redirect
if ($success) {
    header("Location: sukses_pengaduan.php");
    exit;
} else {
    echo "Terjadi kesalahan saat menyimpan data.";
}
?>
