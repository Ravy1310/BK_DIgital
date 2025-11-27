<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../logAktivitas.php'; // Tambahkan require log aktivitas

// GET ID
$id_soal = isset($_GET['id_soal']) ? intval($_GET['id_soal']) : 0;
$id_tes  = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;

if ($id_soal <= 0) {
    die("ID soal tidak valid");
}

try {
    // AMBIL DATA SOAL SEBELUM DIHAPUS UNTUK LOG
    $stmt_get_soal = $pdo->prepare("SELECT pertanyaan FROM soal_tes WHERE id_soal = ?");
    $stmt_get_soal->execute([$id_soal]);
    $soal_data = $stmt_get_soal->fetch(PDO::FETCH_ASSOC);
    
    if (!$soal_data) {
        die("Soal tidak ditemukan");
    }

    // HITUNG JUMLAH OPSI YANG AKAN DIHAPUS
    $stmt_count_opsi = $pdo->prepare("SELECT COUNT(*) FROM opsi_jawaban WHERE id_soal = ?");
    $stmt_count_opsi->execute([$id_soal]);
    $jumlah_opsi = $stmt_count_opsi->fetchColumn();

    $pdo->beginTransaction();

    // 1. HAPUS OPSI JAWABAN TERKAIT
    $stmt_delete_opsi = $pdo->prepare("DELETE FROM opsi_jawaban WHERE id_soal = ?");
    $stmt_delete_opsi->execute([$id_soal]);

    // 2. HAPUS SOAL
    $stmt_delete_soal = $pdo->prepare("DELETE FROM soal_tes WHERE id_soal = ?");
    $stmt_delete_soal->execute([$id_soal]);

    $pdo->commit();

    // LOG AKTIVITAS: Hapus soal
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    $log_description = "Menghapus soal (ID: $id_soal) dari tes (ID: $id_tes) beserta $jumlah_opsi opsi jawaban";
    $log_meta = [
        'id_soal' => $id_soal,
        'id_tes' => $id_tes,
        'pertanyaan' => $soal_data['pertanyaan'],
        'jumlah_opsi_dihapus' => $jumlah_opsi,
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ];
    
    log_action('HAPUS_SOAL', $log_description, $log_meta);

    header("Location: ../../pages/admin/kelolasoal.php?id_tes=" . $id_tes . "&status=soal_deleted");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    
    // LOG ERROR jika terjadi kegagalan
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    log_action('ERROR_HAPUS_SOAL', "Gagal menghapus soal: " . $e->getMessage(), [
        'id_soal' => $id_soal,
        'id_tes' => $id_tes,
        'error' => $e->getMessage(),
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ]);
    
    die("Gagal menghapus soal: " . $e->getMessage());
}