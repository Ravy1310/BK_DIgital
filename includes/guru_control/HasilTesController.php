<?php
// HasilTesController.php
session_start();

// Pastikan hanya menerima request POST untuk AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Cek apakah user sudah login (sesuaikan dengan session Anda)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

require_once __DIR__ . '/../../includes/db_connection.php';

// Set header JSON dengan charset UTF-8
header('Content-Type: application/json; charset=utf-8');

// Matikan error reporting untuk produksi (untuk testing bisa diaktifkan)
error_reporting(0);
ini_set('display_errors', 0);

class HasilTesController {
    
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get hasil tes data with filters
    public function getHasilTes($filters = [], $page = 1) {
        try {
            $itemsPerPage = 10;
            $offset = ($page - 1) * $itemsPerPage;
            
            // Build WHERE clause
            $whereClause = "1=1";
            $params = [];
            
            if (!empty($filters['search'])) {
                $whereClause .= " AND (s.nama LIKE ? OR s.kelas LIKE ?)";
                $params[] = '%' . $filters['search'] . '%';
                $params[] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['kelas'])) {
                $whereClause .= " AND s.kelas = ?";
                $params[] = $filters['kelas'];
            }
            
            if (!empty($filters['jenis_tes'])) {
                $whereClause .= " AND t.kategori_tes = ?";
                $params[] = $filters['jenis_tes'];
            }
            
            // Main query
            $sql = "SELECT 
                    ht.id_hasil,
                    ht.id_siswa,
                    ht.id_tes,
                    ht.nilai,
                    ht.jawaban,
                    ht.tanggal_submit,
                    s.nama AS nama_siswa,
                    s.kelas,
                    t.kategori_tes
                FROM hasil_tes ht
                JOIN siswa s ON ht.id_siswa = s.id_siswa
                JOIN tes t ON ht.id_tes = t.id_tes
                WHERE $whereClause
                ORDER BY ht.tanggal_submit DESC
                LIMIT $itemsPerPage OFFSET $offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format data
            foreach ($data as &$row) {
                $row['tanggal_formatted'] = date('d M Y', strtotime($row['tanggal_submit']));
            }
            
            // Count total rows
            $countSql = "SELECT COUNT(*) as total 
                       FROM hasil_tes ht
                       JOIN siswa s ON ht.id_siswa = s.id_siswa
                       JOIN tes t ON ht.id_tes = t.id_tes
                       WHERE $whereClause";
            
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($params);
            $totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
            $totalRows = $totalRow['total'];
            $totalPages = ceil($totalRows / $itemsPerPage);
            
            return [
                'success' => true,
                'data' => $data,
                'totalRows' => $totalRows,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Get statistics - DIPERBAIKI
    public function getStatistik() {
        try {
            // 1. Total seluruh siswa dari tabel siswa
            $sql1 = "SELECT COUNT(*) as total FROM siswa ";
            $stmt1 = $this->pdo->query($sql1);
            $totalRow = $stmt1->fetch(PDO::FETCH_ASSOC);
            $totalSiswa = $totalRow ? $totalRow['total'] : 0;
            
            // 2. Total siswa yang mengerjakan tes (DISTINCT untuk menghindari duplikat)
            $sql2 = "SELECT COUNT(DISTINCT id_siswa) as selesai FROM hasil_tes";
            $stmt2 = $this->pdo->query($sql2);
            $selesaiRow = $stmt2->fetch(PDO::FETCH_ASSOC);
            $selesai = $selesaiRow ? $selesaiRow['selesai'] : 0;
            
            return [
                'success' => true,
                'data' => [
                    'total_siswa' => (int)$totalSiswa,
                    'selesai' => (int)$selesai
                ]
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    // Get detail jawaban
    public function getDetailJawaban($id_hasil) {
        try {
            // 1. Ambil data utama hasil tes
            $sql = "SELECT 
                    ht.*,
                    s.nama AS nama_siswa,
                    s.kelas,
                    t.kategori_tes
                FROM hasil_tes ht
                JOIN siswa s ON ht.id_siswa = s.id_siswa
                JOIN tes t ON ht.id_tes = t.id_tes
                WHERE ht.id_hasil = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_hasil]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ];
            }
            
            // 2. Format tanggal
            $data['tanggal_formatted'] = date('d F Y H:i', strtotime($data['tanggal_submit']));
            
            // 3. Parse jawaban JSON jika ada
            $jawaban_data = [];
            if ($data['jawaban'] && $data['jawaban'] !== 'null') {
                try {
                    $jawaban = json_decode($data['jawaban'], true);
                    
                    if ($jawaban && is_array($jawaban)) {
                        // 4. Ambil detail soal dan opsi untuk setiap jawaban
                        foreach ($jawaban as $id_soal => $id_opsi_dipilih) {
                            // Ambil data soal
                            $sql_soal = "SELECT * FROM soal_tes WHERE id_soal = ?";
                            $stmt_soal = $this->pdo->prepare($sql_soal);
                            $stmt_soal->execute([$id_soal]);
                            $soal = $stmt_soal->fetch(PDO::FETCH_ASSOC);
                            
                            if ($soal) {
                                // Ambil semua opsi untuk soal ini
                                $sql_opsi = "SELECT * FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi";
                                $stmt_opsi = $this->pdo->prepare($sql_opsi);
                                $stmt_opsi->execute([$id_soal]);
                                $opsi_list = $stmt_opsi->fetchAll(PDO::FETCH_ASSOC);
                                
                                // Temukan opsi yang dipilih
                                $opsi_dipilih = null;
                                $bobot_dipilih = 0;
                                foreach ($opsi_list as $opsi) {
                                    if ($opsi['id_opsi'] == $id_opsi_dipilih) {
                                        $opsi_dipilih = $opsi['opsi'];
                                        $bobot_dipilih = $opsi['bobot'];
                                        break;
                                    }
                                }
                                
                                // Simpan data jawaban yang lengkap
                                $jawaban_data[] = [
                                    'id_soal' => $id_soal,
                                    'pertanyaan' => $soal['pertanyaan'],
                                    'id_opsi_dipilih' => $id_opsi_dipilih,
                                    'opsi_dipilih' => $opsi_dipilih ?: 'Tidak ada jawaban',
                                    'bobot_dipilih' => $bobot_dipilih,
                                    'semua_opsi' => $opsi_list
                                ];
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Jika JSON tidak valid, simpan sebagai teks biasa
                    $jawaban_data = $data['jawaban'];
                }
            }
            
            $data['jawaban_detail'] = $jawaban_data;
            
            return [
                'success' => true,
                'data' => $data
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}

// Handle AJAX requests
try {
    // Cek action
    if (!isset($_POST['action'])) {
        throw new Exception('Action tidak ditemukan');
    }
    
    $controller = new HasilTesController($pdo);
    
    switch($_POST['action']) {
        case 'get_data':
            $filters = [
                'search' => $_POST['search'] ?? '',
                'kelas' => $_POST['kelas'] ?? '',
                'jenis_tes' => $_POST['jenis_tes'] ?? ''
            ];
            $page = intval($_POST['page'] ?? 1);
            $result = $controller->getHasilTes($filters, $page);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_statistik':
            $result = $controller->getStatistik();
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_detail_jawaban':
            $id_hasil = intval($_POST['id_hasil'] ?? 0);
            $result = $controller->getDetailJawaban($id_hasil);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action tidak valid'
            ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>