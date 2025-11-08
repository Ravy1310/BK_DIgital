<?php
// File: includes/developer_control/get_activity_logs.php

// TAMBAHKAN SESSION CHECK DI AWAL
session_start();

// CEK APAKAH SUDAH LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Include koneksi database
    require_once __DIR__ . '/../db_connection.php';
    
    // Validasi koneksi database
    if (!$pdo) {
        throw new Exception('Database connection not available');
    }

    // Parameter
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $limit = max(1, min($limit, 100));
    
    // Filter by admin (opsional)
    $admin_filter = isset($_GET['admin_id']) ? (int)$_GET['admin_id'] : null;

    // Query untuk ambil data dari database
    $sql = "SELECT * FROM admin_activity_log ";
    
    // Tambahkan filter jika ada
    if ($admin_filter) {
        $sql .= "WHERE admin_id = :admin_id ";
    }
    
    $sql .= "ORDER BY created_at DESC LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    
    if ($admin_filter) {
        $stmt->bindValue(':admin_id', $admin_filter, PDO::PARAM_INT);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count dari database
    $totalSql = "SELECT COUNT(*) as total FROM admin_activity_log";
    if ($admin_filter) {
        $totalSql .= " WHERE admin_id = :admin_id";
    }
    
    $totalStmt = $pdo->prepare($totalSql);
    if ($admin_filter) {
        $totalStmt->bindValue(':admin_id', $admin_filter, PDO::PARAM_INT);
    }
    $totalStmt->execute();
    $total = $totalStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'total' => (int)$total,
        'message' => 'Data loaded from database',
        'source' => 'database',
        'current_admin' => [
            'id' => $_SESSION['admin_id'] ?? null,
            'name' => $_SESSION['admin_name'] ?? null
        ]
    ]);

} catch (Exception $e) {
    // Fallback ke session storage jika database error
    $logs = isset($_SESSION['activity_logs']) ? $_SESSION['activity_logs'] : [];
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    // Filter by admin jika diperlukan
    $admin_filter = isset($_GET['admin_id']) ? (int)$_GET['admin_id'] : null;
    if ($admin_filter) {
        $logs = array_filter($logs, function($log) use ($admin_filter) {
            return $log['admin_id'] == $admin_filter;
        });
    }
    
    $limitedLogs = array_slice($logs, 0, $limit);
    
    echo json_encode([
        'success' => true,
        'logs' => $limitedLogs,
        'total' => count($logs),
        'message' => 'Data loaded from session (fallback)',
        'source' => 'session',
        'current_admin' => [
            'id' => $_SESSION['admin_id'] ?? null,
            'name' => $_SESSION['admin_name'] ?? null
        ]
    ]);
}

exit;
?>