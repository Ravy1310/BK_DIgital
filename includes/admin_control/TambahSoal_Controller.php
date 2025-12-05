<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "Error: Unauthorized";
    exit;
}

if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    echo "Error: Unauthorized";
    exit;
}

require_once '../db_connection.php';
require_once '../logAktivitas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tes = intval($_POST['id_tes'] ?? 0);
    $pertanyaan = trim($_POST['pertanyaan'] ?? '');
    $opsi = $_POST['opsi'] ?? [];
    $bobot = $_POST['bobot'] ?? [];

    $opsi = array_map('trim', $opsi);
    $bobot = array_map('intval', $bobot);

    $validCount = 0;
    foreach ($opsi as $o) if ($o !== '') $validCount++;

    if (!$pertanyaan || $validCount < 2) {
        echo "Error: Pertanyaan dan minimal 2 opsi wajib diisi.";
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Simpan soal
        $stmt = $pdo->prepare("INSERT INTO soal_tes (id_tes, pertanyaan, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$id_tes, $pertanyaan]);
        $id_soal = $pdo->lastInsertId();

        // Simpan opsi jawaban
        $opsi_count = 0;
        foreach ($opsi as $i => $opsi_teks) {
            if ($opsi_teks === '') continue;
            $b = $bobot[$i] ?? 1;
            $stmt_opsi = $pdo->prepare(
                "INSERT INTO opsi_jawaban (id_soal, opsi, bobot, created_at) VALUES (?, ?, ?, NOW())"
            );
            $stmt_opsi->execute([$id_soal, $opsi_teks, $b]);
            $opsi_count++;
        }

        $pdo->commit();

        // LOG AKTIVITAS: Tambah soal baru
        $adminId = $_SESSION['admin_id'] ?? 0;
        $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
        
        $log_description = "Menambahkan soal baru (ID: $id_soal) ke tes (ID: $id_tes) dengan $opsi_count opsi jawaban";
        $log_meta = [
            'id_soal' => $id_soal,
            'id_tes' => $id_tes,
            'pertanyaan' => $pertanyaan,
            'jumlah_opsi' => $opsi_count,
            'admin_id' => $adminId,
            'admin_name' => $adminName
        ];
        
        log_action('TAMBAH_SOAL', $log_description, $log_meta);

        echo "Soal berhasil ditambahkan!";

    } catch (Exception $e) {
        $pdo->rollBack();
        
        // LOG ERROR jika terjadi kegagalan
        $adminId = $_SESSION['admin_id'] ?? 0;
        $adminName = $_SESSION['admin_name'] ?? 'Unknown Admin';
        
        log_action('ERROR_TAMBAH_SOAL', "Gagal menambahkan soal: " . $e->getMessage(), [
            'id_tes' => $id_tes,
            'error' => $e->getMessage(),
            'admin_id' => $adminId,
            'admin_name' => $adminName
        ]);
        
        echo "Error: Gagal menyimpan soal: " . $e->getMessage();
    }
}
?>