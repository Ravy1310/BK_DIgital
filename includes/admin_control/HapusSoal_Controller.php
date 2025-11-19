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

// GET ID
$id_soal = isset($_GET['id_soal']) ? intval($_GET['id_soal']) : 0;
$id_tes  = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;

if ($id_soal <= 0) {
    die("ID soal tidak valid");
}

try {
    $pdo->beginTransaction();

    // 1. HAPUS OPSI JAWABAN TERKAIT
    $stmt_delete_opsi = $pdo->prepare("DELETE FROM opsi_jawaban WHERE id_soal = ?");
    $stmt_delete_opsi->execute([$id_soal]);

    // 2. HAPUS SOAL
    $stmt_delete_soal = $pdo->prepare("DELETE FROM soal_tes WHERE id_soal = ?");
    $stmt_delete_soal->execute([$id_soal]);

    $pdo->commit();

    header("Location: ../../pages/admin/kelolasoal.php?id_tes=" . $id_tes . "&status=soal_deleted");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal menghapus soal: " . $e->getMessage());
}
