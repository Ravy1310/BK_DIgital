<?php
session_start();

header("Content-Type: application/json");

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(["success" => false, "message" => "Akses ditolak."]);
    exit;
}

$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

$id_tes = isset($_POST['id_tes']) ? intval($_POST['id_tes']) : 0;

if ($id_tes <= 0) {
    echo json_encode(["success" => false, "message" => "ID Tes tidak valid."]);
    exit;
}

try {
    // START TRANSAKSI
    $pdo->beginTransaction();

    // 1️⃣ AMBIL SEMUA ID SOAL YANG TERKAIT
    $stmt = $pdo->prepare("SELECT id_soal FROM soal_tes WHERE id_tes = ?");
    $stmt->execute([$id_tes]);
    $soal_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 2️⃣ HAPUS OPSI JAWABAN UNTUK SETIAP SOAL
    if (!empty($soal_ids)) {
        $in = implode(",", array_fill(0, count($soal_ids), "?"));
        $stmt = $pdo->prepare("DELETE FROM opsi_jawaban WHERE id_soal IN ($in)");
        $stmt->execute($soal_ids);
    }

    // 3️⃣ HAPUS SOAL
    $stmt = $pdo->prepare("DELETE FROM soal_tes WHERE id_tes = ?");
    $stmt->execute([$id_tes]);

    // 4️⃣ HAPUS TES
    $stmt = $pdo->prepare("DELETE FROM tes WHERE id_tes = ?");
    $stmt->execute([$id_tes]);

    // SELESAI
    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Tes berhasil dihapus beserta semua soal & opsinya."
    ]);
} 
catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        "success" => false,
        "message" => "Gagal menghapus tes: " . $e->getMessage()
    ]);
}
?>
