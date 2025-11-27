<?php
// includes/admin_control/ToggleStatusTes_Controller.php

session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../db_connection.php';
require_once '../logAktivitas.php';

header('Content-Type: application/json');

// Debug logging
error_log("ToggleStatusTes_Controller called: " . date('Y-m-d H:i:s'));
error_log("POST data: " . print_r($_POST, true));

try {
    // Validasi input
    if (!isset($_POST['id_tes']) || empty($_POST['id_tes'])) {
        throw new Exception('ID tes tidak valid');
    }
    
    if (!isset($_POST['action']) || !in_array($_POST['action'], ['aktif', 'nonaktif'])) {
        throw new Exception('Aksi tidak valid. Diterima: ' . ($_POST['action'] ?? 'null'));
    }
    
    $id_tes = (int)$_POST['id_tes'];
    $action = $_POST['action'];
    $new_status = $action; // 'aktif' atau 'nonaktif'
    
    error_log("Processing: ID=$id_tes, Action=$action, NewStatus=$new_status");
    
    // Cek apakah tes exists
    $check_sql = "SELECT kategori_tes, status FROM tes WHERE id_tes = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$id_tes]);
    $tes_data = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tes_data) {
        throw new Exception('Tes tidak ditemukan dengan ID: ' . $id_tes);
    }
    
    error_log("Tes ditemukan: " . $tes_data['kategori_tes'] . ", Status saat ini: " . $tes_data['status']);
    
    // Update status
    $update_sql = "UPDATE tes SET status = ?, updated_at = NOW() WHERE id_tes = ?";
    $update_stmt = $pdo->prepare($update_sql);
    
    $result = $update_stmt->execute([$new_status, $id_tes]);
    
    if (!$result) {
        $error_info = $update_stmt->errorInfo();
        throw new Exception('Gagal mengupdate status tes: ' . $error_info[2]);
    }
    
    $affected_rows = $update_stmt->rowCount();
    error_log("Update berhasil. Affected rows: " . $affected_rows);
    
    // Log aktivitas
    $action_type = $new_status === 'aktif' ? 'ACTIVATE_TEST' : 'DEACTIVATE_TEST';
    $action_desc = $new_status === 'aktif' ? 
        "Mengaktifkan tes: {$tes_data['kategori_tes']}" : 
        "Menonaktifkan tes: {$tes_data['kategori_tes']}";
    
    log_action(
        $action_type,
        $action_desc,
        [
            'tes_id' => $id_tes,
            'nama_tes' => $tes_data['kategori_tes'],
            'status_sebelumnya' => $tes_data['status'],
            'status_baru' => $new_status
        ]
    );
    
    echo json_encode([
        'success' => true,
        'message' => "Tes '{$tes_data['kategori_tes']}' berhasil di" . ($new_status === 'aktif' ? 'aktifkan' : 'nonaktifkan')
    ]);
    
} catch (Exception $e) {
    error_log("Error in ToggleStatusTes_Controller: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>