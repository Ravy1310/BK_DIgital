<?php
// File: controllers/JadwalController.php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Silakan login kembali.']);
    exit;
}

// Cek apakah user adalah guru
$isGuru = ($_SESSION['admin_role'] === 'user' && isset($_SESSION['is_guru']) && $_SESSION['is_guru'] === true);
if (!$isGuru) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Hanya guru yang dapat mengakses.']);
    exit;
}

// Include database connection
require_once __DIR__ . '/../../includes/db_connection.php';

// Set header JSON
header('Content-Type: application/json');

// Ambil ID guru dari session
$id_guru = $_SESSION['guru_id'] ?? null;
if (!$id_guru) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        handleGetRequest($pdo, $id_guru);
    } elseif ($method === 'POST') {
        handlePostRequest($pdo, $id_guru);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function handleGetRequest($pdo, $id_guru) {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? 0;
    
    if ($action === 'get_detail' && $id) {
        try {
            $query = "
                SELECT 
                    jk.*,
                    s.nama,
                    s.kelas,
                    g.nama as nama_guru
                FROM jadwal_konseling jk
                LEFT JOIN siswa s ON jk.id_siswa = s.id_siswa
                LEFT JOIN guru g ON jk.id_guru = g.id_guru
                WHERE jk.id_jadwal = :id_jadwal
                AND jk.id_guru = :id_guru
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':id_jadwal' => $id,
                ':id_guru' => $id_guru
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Normalize status
                $result['Status'] = normalizeStatus($result['Status']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Detail retrieved successfully',
                    'data' => $result
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Data not found atau tidak memiliki akses'
                ]);
            }
        } catch (Exception $e) {
            throw new Exception("Error getting detail: " . $e->getMessage());
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action or missing ID'
        ]);
    }
}

function handlePostRequest($pdo, $id_guru) {
    $action = $_POST['action'] ?? '';
    $id_jadwal = $_POST['id_jadwal'] ?? 0;
    
    if (!$id_jadwal) {
        echo json_encode([
            'success' => false,
            'message' => 'ID jadwal diperlukan'
        ]);
        return;
    }
    
    // Validasi apakah jadwal milik guru ini
    if (!validateJadwalOwnership($pdo, $id_jadwal, $id_guru)) {
        echo json_encode([
            'success' => false,
            'message' => 'Anda tidak memiliki akses ke jadwal ini'
        ]);
        return;
    }
    
    switch ($action) {
        case 'setujui': // Tombol di UI menggunakan 'setujui'
            $success = updateStatus($pdo, $id_jadwal, 'Disetujui', 'Jadwal disetujui oleh guru BK');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Jadwal berhasil disetujui' : 'Gagal menyetujui jadwal'
            ]);
            break;
            
        case 'jadwalkan_ulang':
            $success = updateStatusToReschedule($pdo, $id_jadwal);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Status berhasil diubah menjadi Jadwalkan Ulang' : 'Gagal mengubah status'
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Aksi tidak valid: ' . $action
            ]);
    }
}

function validateJadwalOwnership($pdo, $id_jadwal, $id_guru) {
    try {
        $query = "SELECT id_jadwal FROM jadwal_konseling WHERE id_jadwal = :id_jadwal AND id_guru = :id_guru";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id_jadwal' => $id_jadwal,
            ':id_guru' => $id_guru
        ]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Error validating ownership: " . $e->getMessage());
        return false;
    }
}

function updateStatus($pdo, $id_jadwal, $status, $keterangan = '') {
    try {
        $query = "
            UPDATE jadwal_konseling 
            SET Status = :status, 
                keterangan = :keterangan,
                updated_at = NOW()
            WHERE id_jadwal = :id_jadwal
        ";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':status' => $status,
            ':keterangan' => $keterangan,
            ':id_jadwal' => $id_jadwal
        ]);
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in updateStatus: " . $e->getMessage());
        return false;
    }
}

function updateStatusToReschedule($pdo, $id_jadwal) {
    try {
        $query = "
            UPDATE jadwal_konseling 
            SET Status = 'Jadwalkan Ulang',
                keterangan = CONCAT('Jadwal perlu diatur ulang oleh siswa (diubah oleh guru pada ', DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i'), ')'),
                updated_at = NOW()
            WHERE id_jadwal = :id_jadwal
        ";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            ':id_jadwal' => $id_jadwal
        ]);
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in updateStatusToReschedule: " . $e->getMessage());
        return false;
    }
}

function normalizeStatus($status) {
    if ($status === null) return 'Menunggu';
    
    $status = trim($status);
    $lowerStatus = strtolower($status);
    
    $statusMap = [
        'dijadwalkan ulang' => 'Jadwalkan Ulang',
        'jadwalkan ulang' => 'Jadwalkan Ulang',
        'terima' => 'Disetujui',
        'diterima' => 'Disetujui',
        'setujui' => 'Disetujui',
        'menunggu konfirmasi' => 'Menunggu',
        'pending' => 'Menunggu',
        'waiting' => 'Menunggu'
    ];
    
    if (isset($statusMap[$lowerStatus])) {
        return $statusMap[$lowerStatus];
    }
    
    $validStatuses = ['Menunggu', 'Disetujui', 'Jadwalkan Ulang'];
    if (in_array($status, $validStatuses)) {
        return $status;
    }
    
    return 'Menunggu';
}
?>