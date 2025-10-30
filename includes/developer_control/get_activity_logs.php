<?php
// File: includes/developer_control/get_activity_logs.php

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

    // Query untuk ambil data dari database
    $sql = "SELECT * FROM admin_activity_log 
            ORDER BY created_at DESC 
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count dari database
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM admin_activity_log");
    $total = $totalStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'logs' => $logs, // Biarkan kosong jika tidak ada data
        'total' => (int)$total,
        'message' => 'Data loaded from database',
        'source' => 'database'
    ]);

} catch (Exception $e) {
    // Fallback ke session storage jika database error
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $logs = isset($_SESSION['activity_logs']) ? $_SESSION['activity_logs'] : [];
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $limitedLogs = array_slice($logs, 0, $limit);
    
    echo json_encode([
        'success' => true,
        'logs' => $limitedLogs, // Biarkan kosong jika tidak ada data
        'total' => count($logs),
        'message' => 'Data loaded from session (fallback)',
        'source' => 'session'
    ]);
}

exit;
?>