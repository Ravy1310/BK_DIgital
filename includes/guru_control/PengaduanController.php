<?php
// File: controllers/PengaduanController.php

require_once __DIR__ . '/../../includes/db_connection.php';

class PengaduanController {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }
    
    /**
     * Inisialisasi manajemen pengaduan
     */
    public function initManajemenPengaduan() {
        try {
            $query = "SELECT 
                        p.id_pengaduan,
                        p.id_siswa,
                        p.jenis_laporan,
                        p.jenis_kejadian,
                        p.deskripsi,
                        p.status,
                        DATE_FORMAT(p.tanggal_pengaduan, '%d %M %Y pukul %H:%i') as tanggal_format,
                        s.nama,
                        s.kelas
                      FROM pengaduan p
                      LEFT JOIN siswa s ON p.id_siswa = s.id_siswa
                      ORDER BY p.tanggal_pengaduan DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $pengaduans = $stmt->fetchAll();
            
            return $this->generateHTML($pengaduans);
            
        } catch (PDOException $e) {
            return $this->generateErrorHTML("Terjadi kesalahan: " . $e->getMessage());
        }
    }
    
    /**
     * Generate HTML untuk tabel pengaduan
     */
    private function generateHTML($pengaduans) {
        $html = '';
        
        if (empty($pengaduans)) {
            $html = '<tr><td colspan="5" class="text-center py-4">Tidak ada pengaduan ditemukan</td></tr>';
            return $html;
        }
        
        foreach ($pengaduans as $pengaduan) {
            $nama_pelapor = ($pengaduan['id_siswa'] === null) ? 'Anonim' : $pengaduan['nama'];
            $kelas = ($pengaduan['id_siswa'] !== null && !empty($pengaduan['kelas_siswa'])) ? 
                     ' (' . $pengaduan['kelas_siswa'] . ')' : '';
            
            // Tentukan kelas status
            $status_class = $this->getStatusClass($pengaduan['status']);
            
            $html .= '<tr>';
            $html .= '<td class="fw-medium text-start">' . htmlspecialchars($pengaduan['jenis_laporan']) . '</td>';
            $html .= '<td class="text-center">' . htmlspecialchars($nama_pelapor . $kelas) . '</td>';
            $html .= '<td class="text-center">' . $pengaduan['tanggal_format'] . '</td>';
            $html .= '<td class="text-center"><span class="status-btn ' . $status_class . '">' . $pengaduan['status'] . '</span></td>';
            $html .= '<td class="text-center">';
            $html .= '<a class="action-link" data-bs-toggle="modal" data-bs-target="#detailModal" ';
            $html .= 'data-id="' . $pengaduan['id_pengaduan'] . '" ';
            $html .= 'data-subject="' . htmlspecialchars($pengaduan['jenis_laporan']) . '" ';
            $html .= 'data-reporter="' . htmlspecialchars($nama_pelapor . $kelas) . '" ';
            $html .= 'data-date="' . $pengaduan['tanggal_format'] . '" ';
            $html .= 'data-status="' . $pengaduan['status'] . '" ';
            $html .= 'data-jenis-kejadian="' . htmlspecialchars($pengaduan['jenis_kejadian']) . '" ';
            $html .= 'data-message="' . htmlspecialchars($pengaduan['deskripsi']) . '">';
            $html .= '<i class="bi bi-eye"></i> Lihat</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        return $html;
    }
    
    /**
     * Get status class berdasarkan status
     */
    private function getStatusClass($status) {
        // Normalisasi status untuk case-insensitive
        $statusLower = strtolower(trim($status));
        
        switch ($statusLower) {
            case 'baru':
            case 'menunggu':
                return 'status-new';
            case 'diproses':
                return 'status-process';
            case 'selesai':
                return 'status-done';
            default:
                return 'status-new';
        }
    }
    
    /**
     * Generate error HTML
     */
    private function generateErrorHTML($message) {
        return '<tr><td colspan="5" class="text-center py-4 text-danger">' . $message . '</td></tr>';
    }
    
    /**
     * Update status pengaduan
     */
    public function updateStatusPengaduan($id_pengaduan, $status) {
        try {
            $query = "UPDATE pengaduan SET status = :status WHERE id_pengaduan = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id_pengaduan, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error update status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Handle AJAX request - INI YANG PERLU DITAMBAHKAN
     */
    public function handleAjaxRequest() {
        // Set header JSON untuk semua response AJAX
        header('Content-Type: application/json');
        
        // Cek jika ini request AJAX untuk update status
        if (isset($_GET['action']) && $_GET['action'] === 'update_status') {
            
            // Ambil data dari POST
            $id_pengaduan = $_POST['id_pengaduan'] ?? null;
            $status = $_POST['status'] ?? null;
            
            // Validasi input
            if (!$id_pengaduan || !$status) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
                exit;
            }
            
            // Update status
            $success = $this->updateStatusPengaduan($id_pengaduan, $status);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Status berhasil diubah',
                    'new_status' => $status
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengubah status'
                ]);
            }
            exit;
        }
    }
}

// Inisialisasi controller
$controller = new PengaduanController();

// Handle AJAX request jika ada parameter action
if (isset($_GET['action'])) {
    $controller->handleAjaxRequest();
    exit; // Stop eksekusi selanjutnya untuk response AJAX
}

// Jika bukan AJAX request, jalankan fungsi normal
// (ini biasanya dipanggil dari manajemenpengaduan.php)