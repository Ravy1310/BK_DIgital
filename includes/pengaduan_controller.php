<?php
session_start();
require_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SAMAKAN DENGAN FORM
    $jenis_laporan  = $_POST["jenis_laporan"] ?? null;  
    $jenis_kejadian = $_POST["jenis_kejadian"] ?? null; 
    $isi_aduan      = $_POST["penjelasan"] ?? null;
    $id_siswa       = $_POST["id_siswa"] ?? null;

    // VALIDASI
    if (!$jenis_laporan || !$jenis_kejadian || !$isi_aduan) {
        $_SESSION["error"] = "Semua field wajib diisi!";
        header("Location: ../pages/Siswa/pengaduan.php");
        exit;
    }

    // HANDLE ANONIM
    if ($jenis_laporan === "Anonim") {
        $id_siswa = null;
        $kode_hash = hash("sha256", time() . rand());
    } else {
        if (!$id_siswa) {
            $_SESSION["error"] = "ID siswa tidak valid untuk laporan teridentifikasi.";
            header("Location: ../pages/Siswa/buat_aduan.php");
            exit;
        }
        $kode_hash = hash("sha256", $id_siswa . time());
    }

    // SIMPAN KE DATABASE
    $sql = "INSERT INTO pengaduan 
            (id_siswa, kode_hash, jenis_aduan, jenis_kejadian, isi_aduan, tanggal_pengaduan)
            VALUES 
            (:id_siswa, :kode_hash, :jenis_aduan, :jenis_kejadian, :isi_aduan, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id_siswa"       => $id_siswa,
        ":kode_hash"      => $kode_hash,
        ":jenis_aduan"    => $jenis_laporan,
        ":jenis_kejadian" => $jenis_kejadian,
        ":isi_aduan"      => $isi_aduan
    ]);

    $_SESSION["success"] = "Pengaduan berhasil dikirim!";
    header("Location: ../pages/Siswa/riwayat_aduan.php");
    exit;
}
?>
