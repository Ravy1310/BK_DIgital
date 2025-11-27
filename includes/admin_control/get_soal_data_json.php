<?php
// includes/admin_control/get_soal_data_json.php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$id_soal = isset($_GET['id_soal']) ? intval($_GET['id_soal']) : 0;

if ($id_soal <= 0) {
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['success' => false, 'error' => 'ID Soal tidak valid']));
}

// DB
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

header('Content-Type: application/json');

try {
    // AMBIL DATA SOAL
    $stmt = $pdo->prepare("SELECT * FROM soal_tes WHERE id_soal = ? LIMIT 1");
    $stmt->execute([$id_soal]);
    $soal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$soal) {
        header('HTTP/1.1 404 Not Found');
        die(json_encode(['success' => false, 'error' => 'Soal tidak ditemukan']));
    }

    // AMBIL OPSI beserta bobot
    $stmt_opsi = $pdo->prepare("SELECT * FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi ASC");
    $stmt_opsi->execute([$id_soal]);
    $opsi_list = $stmt_opsi->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'soal' => $soal,
        'opsi_list' => $opsi_list
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>