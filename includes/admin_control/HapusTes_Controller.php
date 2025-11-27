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
require_once $base_dir . 'includes/logAktivitas.php';

$id_tes = isset($_POST['id_tes']) ? intval($_POST['id_tes']) : 0;

if ($id_tes <= 0) {
    echo json_encode(["success" => false, "message" => "ID Tes tidak valid."]);
    exit;
}

try {
    // AMBIL DATA TES SEBELUM DIHAPUS UNTUK LOG
    $stmt_info = $pdo->prepare("SELECT kategori_tes, deskripsi_tes FROM tes WHERE id_tes = ?");
    $stmt_info->execute([$id_tes]);
    $tes_info = $stmt_info->fetch(PDO::FETCH_ASSOC);
    
    if (!$tes_info) {
        echo json_encode(["success" => false, "message" => "Tes tidak ditemukan."]);
        exit;
    }

    // HITUNG JUMLAH SOAL YANG AKAN DIHAPUS
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM soal_tes WHERE id_tes = ?");
    $stmt_count->execute([$id_tes]);
    $jumlah_soal = $stmt_count->fetchColumn();

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

    // LOG AKTIVITAS: Hapus tes
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    $log_description = "Menghapus tes '{$tes_info['kategori_tes']}' (ID: $id_tes) beserta $jumlah_soal soal";
    $log_meta = [
        'id_tes' => $id_tes,
        'kategori_tes' => $tes_info['kategori_tes'],
        'deskripsi_tes' => $tes_info['deskripsi_tes'],
        'jumlah_soal_dihapus' => $jumlah_soal,
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ];
    
    log_action('HAPUS_TES', $log_description, $log_meta);

    echo json_encode([
        "success" => true,
        "message" => "Tes '{$tes_info['kategori_tes']}' berhasil dihapus beserta $jumlah_soal soal & opsinya."
    ]);
} 
catch (Exception $e) {
    $pdo->rollBack();
    
    // LOG ERROR jika terjadi kegagalan
    $adminId = $_SESSION['admin_id'] ?? 0;
    $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
    
    log_action('ERROR_HAPUS_TES', "Gagal menghapus tes: " . $e->getMessage(), [
        'id_tes' => $id_tes,
        'error' => $e->getMessage(),
        'admin_id' => $adminId,
        'admin_name' => $adminName
    ]);
    
    echo json_encode([
        "success" => false,
        "message" => "Gagal menghapus tes: " . $e->getMessage()
    ]);
}
?>