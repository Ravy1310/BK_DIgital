<?php
// File: includes/logAktivitas.php

function log_activity($pdo, $adminId, $adminName, $action, $description, $meta = null) {
    // Jika $pdo tidak ada (null), gunakan session storage
    if ($pdo === null) {
        return log_activity_session($adminId, $adminName, $action, $description, $meta);
    }
    
    // Jika $pdo ada, gunakan database (untuk future use)
    $sql = "INSERT INTO admin_activity_log (admin_id, admin_name, action, description, meta)
            VALUES (:admin_id, :admin_name, :action, :description, :meta)";
    $stmt = $pdo->prepare($sql);

    $metaJson = $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;

    return $stmt->execute([
        ':admin_id'   => $adminId,
        ':admin_name' => $adminName,
        ':action'     => $action,
        ':description'=> $description,
        ':meta'       => $metaJson
    ]);
}

// Fungsi untuk simpan log di session
function log_activity_session($adminId, $adminName, $action, $description, $meta = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Inisialisasi session storage jika belum ada
    if (!isset($_SESSION['activity_logs'])) {
        $_SESSION['activity_logs'] = [];
    }
    
    // Buat log entry
    $logEntry = [
        'id' => count($_SESSION['activity_logs']) + 1,
        'admin_id' => $adminId,
        'admin_name' => $adminName,
        'action' => $action,
        'description' => $description,
        'meta' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Tambahkan ke session
    $_SESSION['activity_logs'][] = $logEntry;
    
    return true;
}

// Fungsi helper
function log_action($action, $description, $meta = null) {
    global $pdo;
    
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $adminId = $_SESSION['admin_id'] ?? 1;
    $adminName = $_SESSION['admin_name'] ?? 'System';
    
    return log_activity($pdo, $adminId, $adminName, $action, $description, $meta);
}
?>