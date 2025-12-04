<?php
// File: controllers/LaporanKonselingController.php

// TURN ON ERROR REPORTING untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// START SESSION DI AWAL FILE - HARUS PERTAMA
session_start();

// Pastikan tidak ada output sebelum ini
if (ob_get_length()) ob_clean();

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Silakan login kembali.']);
    exit;
}

// Cek apakah user adalah guru
$isGuru = ($_SESSION['admin_role'] === 'user' && isset($_SESSION['is_guru']) && $_SESSION['is_guru'] === true);
if (!$isGuru) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied. Hanya guru yang dapat mengakses.']);
    exit;
}

// Include database connection
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// Set header JSON - HARUS SEBELUM OUTPUT APAPUN
header('Content-Type: application/json');

// Ambil ID guru dari session
$id_guru = $_SESSION['guru_id'] ?? null;
if (!$id_guru) {
    echo json_encode(['success' => false, 'message' => 'Data guru tidak ditemukan.']);
    exit;
}

// Function untuk handle error dan tetap return JSON
function handleError($message, $error = null) {
    error_log("LaporanKonselingController Error: " . $message . ($error ? " - " . $error->getMessage() : ""));
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $message,
        'error' => $error ? $error->getMessage() : null
    ]);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get_laporan':
                getLaporan($pdo, $id_guru);
                break;
            case 'get_detail':
                getDetailLaporan($pdo, $id_guru);
                break;
            case 'get_jadwal_available':
                getJadwalAvailable($pdo, $id_guru);
                break;
            case 'search':
                searchLaporan($pdo, $id_guru);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
        
    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                createLaporan($pdo, $id_guru);
                break;
            case 'update':
                updateLaporan($pdo, $id_guru);
                break;
            case 'delete':
                deleteLaporan($pdo, $id_guru);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
    
} catch (Exception $e) {
    handleError('Server error', $e);
}

// ================= FUNGSI GET JADWAL AVAILABLE =================
function getJadwalAvailable($pdo, $id_guru) {
    try {
        error_log("Getting available jadwal for guru ID: " . $id_guru);
        
        // QUERY SEDERHANA - Pastikan berhasil
        $query = "
            SELECT 
                jk.id_jadwal,
                jk.Tanggal_Konseling,
                jk.Waktu_Konseling,
                jk.Topik_konseling,
                jk.Status,
                s.nama as nama_siswa,
                s.kelas
            FROM jadwal_konseling jk
            INNER JOIN siswa s ON jk.id_siswa = s.id_siswa
            WHERE jk.id_guru = :id_guru
            AND jk.Status = 'Disetujui'
            ORDER BY jk.Tanggal_Konseling DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id_guru' => $id_guru]);
        $allJadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($allJadwal) . " jadwal with status 'Disetujui'");
        
        // Filter untuk yang belum ada laporan
        $availableJadwal = [];
        
        foreach ($allJadwal as $jadwal) {
            // Cek apakah sudah ada laporan
            $checkQuery = "SELECT id_laporan FROM laporan_konseling WHERE id_jadwal = :id_jadwal";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute([':id_jadwal' => $jadwal['id_jadwal']]);
            $laporanExists = $checkStmt->fetch();
            
            if (!$laporanExists) {
                // Format tanggal
                if ($jadwal['Tanggal_Konseling']) {
                    try {
                        $date = new DateTime($jadwal['Tanggal_Konseling']);
                        $jadwal['Tanggal_Konseling_formatted'] = $date->format('d F Y');
                    } catch (Exception $e) {
                        $jadwal['Tanggal_Konseling_formatted'] = $jadwal['Tanggal_Konseling'];
                    }
                }
                
                if ($jadwal['Waktu_Konseling'] && $jadwal['Waktu_Konseling'] != '00:00:00') {
                    try {
                        $time = new DateTime($jadwal['Waktu_Konseling']);
                        $jadwal['Waktu_Konseling_formatted'] = $time->format('H.i');
                    } catch (Exception $e) {
                        $jadwal['Waktu_Konseling_formatted'] = substr($jadwal['Waktu_Konseling'], 0, 5);
                    }
                }
                
                $availableJadwal[] = $jadwal;
            }
        }
        
        error_log("Available jadwal (no laporan): " . count($availableJadwal));
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'data' => $availableJadwal,
            'count' => count($availableJadwal),
            'debug_info' => [
                'guru_id' => $id_guru,
                'total_disetujui' => count($allJadwal),
                'available' => count($availableJadwal)
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error in getJadwalAvailable: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data jadwal',
            'error_details' => $e->getMessage()
        ]);
    }
}

// ================= FUNGSI GET LAPORAN =================
function getLaporan($pdo, $id_guru) {
    try {
        $search = $_GET['search'] ?? '';
        
        $query = "
            SELECT 
                lk.id_laporan,
                lk.id_jadwal,
                lk.tanggal_dibuat,
                lk.hasil_laporan,
                lk.catatan_tambahan,
                lk.created_at,
                jk.Tanggal_Konseling,
                jk.Waktu_Konseling,
                jk.Topik_konseling,
                s.nama as nama_siswa,
                s.kelas
            FROM laporan_konseling lk
            INNER JOIN jadwal_konseling jk ON lk.id_jadwal = jk.id_jadwal
            INNER JOIN siswa s ON jk.id_siswa = s.id_siswa
            WHERE lk.id_guru = :id_guru
        ";
        
        $params = [':id_guru' => $id_guru];
        
        if (!empty($search)) {
            $query .= " AND (s.nama LIKE :search OR jk.Topik_konseling LIKE :search2 OR s.kelas LIKE :search3)";
            $searchTerm = '%' . $search . '%';
            $params[':search'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
        }
        
        $query .= " ORDER BY lk.tanggal_dibuat DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format tanggal
        foreach ($results as &$row) {
            // Format Tanggal_Konseling
            if ($row['Tanggal_Konseling']) {
                try {
                    $date = new DateTime($row['Tanggal_Konseling']);
                    $row['Tanggal_Konseling_formatted'] = $date->format('d F Y');
                } catch (Exception $e) {
                    $row['Tanggal_Konseling_formatted'] = $row['Tanggal_Konseling'];
                }
            }
            
            // Format Waktu_Konseling
            if ($row['Waktu_Konseling'] && $row['Waktu_Konseling'] != '00:00:00') {
                try {
                    $time = new DateTime($row['Waktu_Konseling']);
                    $row['Waktu_Konseling_formatted'] = $time->format('H.i');
                } catch (Exception $e) {
                    $row['Waktu_Konseling_formatted'] = substr($row['Waktu_Konseling'], 0, 5);
                }
            }
            
            // Format tanggal_dibuat
            if ($row['tanggal_dibuat']) {
                try {
                    $dateCreated = new DateTime($row['tanggal_dibuat']);
                    $row['tanggal_dibuat_formatted'] = $dateCreated->format('d F Y pukul H.i');
                } catch (Exception $e) {
                    $row['tanggal_dibuat_formatted'] = $row['tanggal_dibuat'];
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data laporan',
            'error' => $e->getMessage()
        ]);
    }
}

// ================= FUNGSI GET DETAIL LAPORAN =================
// Di fungsi getDetailLaporan() dan getLaporan(), perbaiki formatting tanggal:

function getDetailLaporan($pdo, $id_guru) {
    $id_laporan = $_GET['id'] ?? 0;
    
    if (!$id_laporan) {
        echo json_encode(['success' => false, 'message' => 'ID laporan diperlukan']);
        return;
    }
    
    try {
        $query = "
            SELECT 
                lk.*,
                jk.Tanggal_Konseling,
                jk.Waktu_Konseling,
                jk.Topik_konseling,
                s.nama as nama_siswa,
                s.kelas,
                g.nama as nama_guru
            FROM laporan_konseling lk
            INNER JOIN jadwal_konseling jk ON lk.id_jadwal = jk.id_jadwal
            INNER JOIN siswa s ON jk.id_siswa = s.id_siswa
            INNER JOIN guru g ON lk.id_guru = g.id_guru
            WHERE lk.id_laporan = :id_laporan 
            AND lk.id_guru = :id_guru
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id_laporan' => $id_laporan,
            ':id_guru' => $id_guru
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // ================= PERBAIKAN FORMAT TANGGAL =================
            
            // 1. Format Tanggal_Konseling
            if ($result['Tanggal_Konseling'] && $result['Tanggal_Konseling'] != '0000-00-00') {
                try {
                    $date = DateTime::createFromFormat('Y-m-d', $result['Tanggal_Konseling']);
                    if ($date) {
                        // Format Indonesia: 4 Desember 2025
                        $result['Tanggal_Konseling_formatted'] = $date->format('j F Y');
                    } else {
                        $result['Tanggal_Konseling_formatted'] = $result['Tanggal_Konseling'];
                    }
                } catch (Exception $e) {
                    $result['Tanggal_Konseling_formatted'] = $result['Tanggal_Konseling'];
                }
            } else {
                $result['Tanggal_Konseling_formatted'] = '-';
            }
            
            // 2. Format Waktu_Konseling
            if ($result['Waktu_Konseling'] && $result['Waktu_Konseling'] != '00:00:00') {
                try {
                    $time = DateTime::createFromFormat('H:i:s', $result['Waktu_Konseling']);
                    if ($time) {
                        $result['Waktu_Konseling_formatted'] = $time->format('H.i');
                    } else {
                        $result['Waktu_Konseling_formatted'] = substr($result['Waktu_Konseling'], 0, 5);
                    }
                } catch (Exception $e) {
                    $result['Waktu_Konseling_formatted'] = substr($result['Waktu_Konseling'], 0, 5);
                }
            } else {
                $result['Waktu_Konseling_formatted'] = '-';
            }
            
            // 3. Format tanggal_dibuat (Tanggal Pembuatan Laporan)
            if ($result['tanggal_dibuat']) {
                try {
                    // Cek format datetime dari database
                    $formats = ['Y-m-d H:i:s', 'Y-m-d', 'd/m/Y H:i:s', 'd-m-Y H:i:s'];
                    $dateCreated = null;
                    
                    foreach ($formats as $format) {
                        $dateCreated = DateTime::createFromFormat($format, $result['tanggal_dibuat']);
                        if ($dateCreated !== false) {
                            break;
                        }
                    }
                    
                    if ($dateCreated === false) {
                        // Coba parse sebagai string biasa
                        $dateCreated = new DateTime($result['tanggal_dibuat']);
                    }
                    
                    if ($dateCreated) {
                        // Format Indonesia: 4 Desember 2025 pukul 09.02
                        $result['tanggal_dibuat_formatted'] = $dateCreated->format('j F Y') . ' pukul ' . $dateCreated->format('H.i');
                    } else {
                        $result['tanggal_dibuat_formatted'] = $result['tanggal_dibuat'];
                    }
                } catch (Exception $e) {
                    error_log("Error formatting tanggal_dibuat: " . $e->getMessage());
                    $result['tanggal_dibuat_formatted'] = $result['tanggal_dibuat'];
                }
            } else {
                $result['tanggal_dibuat_formatted'] = '-';
            }
            
            // ================= END FORMATTING =================
            
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Error in getDetailLaporan: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil detail laporan',
            'error' => $e->getMessage()
        ]);
    }
}
// ================= FUNGSI CREATE LAPORAN =================
function createLaporan($pdo, $id_guru) {
    $id_jadwal = $_POST['id_jadwal'] ?? 0;
    $hasil_laporan = $_POST['hasil_laporan'] ?? '';
    $catatan_tambahan = $_POST['catatan_tambahan'] ?? '';
    $tanggal_dibuat = $_POST['tanggal_dibuat'] ?? date('Y-m-d H:i:s');
    
    // Validasi
    if (!$id_jadwal) {
        echo json_encode(['success' => false, 'message' => 'ID jadwal diperlukan']);
        return;
    }
    
    if (empty($hasil_laporan)) {
        echo json_encode(['success' => false, 'message' => 'Hasil laporan tidak boleh kosong']);
        return;
    }
    
    try {
        // Cek apakah sudah ada laporan untuk jadwal ini
        $checkQuery = "SELECT id_laporan FROM laporan_konseling WHERE id_jadwal = :id_jadwal";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([':id_jadwal' => $id_jadwal]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Laporan untuk jadwal ini sudah ada']);
            return;
        }
        
        // Insert laporan baru
        $insertQuery = "
            INSERT INTO laporan_konseling 
            (id_jadwal, id_guru, tanggal_dibuat, hasil_laporan, catatan_tambahan) 
            VALUES 
            (:id_jadwal, :id_guru, :tanggal_dibuat, :hasil_laporan, :catatan_tambahan)
        ";
        
        $insertStmt = $pdo->prepare($insertQuery);
        $result = $insertStmt->execute([
            ':id_jadwal' => $id_jadwal,
            ':id_guru' => $id_guru,
            ':tanggal_dibuat' => $tanggal_dibuat,
            ':hasil_laporan' => $hasil_laporan,
            ':catatan_tambahan' => $catatan_tambahan
        ]);
        
        if ($result) {
            $lastId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'id_laporan' => $lastId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal membuat laporan'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal membuat laporan: ' . $e->getMessage()
        ]);
    }
}

// ================= FUNGSI SEARCH LAPORAN =================
function searchLaporan($pdo, $id_guru) {
    getLaporan($pdo, $id_guru);
}

// ================= FUNGSI UPDATE LAPORAN =================
function updateLaporan($pdo, $id_guru) {
    // Implement jika diperlukan
    echo json_encode(['success' => false, 'message' => 'Not implemented yet']);
}

// ================= FUNGSI DELETE LAPORAN =================
function deleteLaporan($pdo, $id_guru) {
    // Implement jika diperlukan
    echo json_encode(['success' => false, 'message' => 'Not implemented yet']);
}

// Pastikan tidak ada output setelah ini
exit;
?>