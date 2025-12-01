<?php
session_start();

// Validasi session
require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";

if (!isVerifiedFor('tes')) {
    $_SESSION['error'] = "Session tidak valid. Silakan login ulang.";
    header("Location: verifikasi_tes.php");
    exit;
}

// Dapatkan data siswa
$siswa_data = getCurrentStudent();
$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];
$kelas_siswa = $siswa_data['kelas'];

// Include controller
require_once __DIR__ . "/../../includes/db_connection.php";
require_once __DIR__ . "/../../includes/siswa_control/tes_controller.php";

// Ambil ID hasil dari URL
$id_hasil = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi ID hasil
if ($id_hasil <= 0) {
    // Coba ambil ID hasil terbaru
    try {
        $stmt = $pdo->prepare("
            SELECT id_hasil FROM hasil_tes 
            WHERE id_siswa = ? 
            ORDER BY tanggal_submit DESC 
            LIMIT 1
        ");
        $stmt->execute([$id_siswa]);
        $id_hasil = $stmt->fetchColumn();
        
        if (!$id_hasil) {
            die("Tidak ada hasil tes ditemukan untuk siswa ini.");
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Ambil data hasil tes
$hasil_tes = getHasilTesById($id_hasil);

// Jika tidak ditemukan dengan fungsi, cari manual
if (!$hasil_tes) {
    try {
        // Cari dengan query langsung
        $stmt = $pdo->prepare("
            SELECT h.*, t.kategori_tes, t.deskripsi_tes, s.nama as nama_siswa, s.kelas as kelas_siswa
            FROM hasil_tes h
            JOIN tes t ON h.id_tes = t.id_tes
            JOIN siswa s ON h.id_siswa = s.id_siswa
            WHERE h.id_hasil = ?
        ");
        $stmt->execute([$id_hasil]);
        $hasil_tes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($hasil_tes) {
            // Parse jawaban manual
            if (!empty($hasil_tes['jawaban'])) {
                $jawaban_data = json_decode($hasil_tes['jawaban'], true);
                $hasil_tes['jawaban_detail'] = [];
                
                if (is_array($jawaban_data) && !empty($jawaban_data)) {
                    foreach ($jawaban_data as $id_soal => $id_opsi) {
                        $id_soal = (int)$id_soal;
                        $id_opsi = (int)$id_opsi;
                        
                        if ($id_soal <= 0 || $id_opsi <= 0) {
                            continue;
                        }
                        
                        // Ambil detail soal dan opsi
                        $stmtDetail = $pdo->prepare("
                            SELECT st.pertanyaan, oj.opsi, oj.bobot
                            FROM soal_tes st
                            JOIN opsi_jawaban oj ON st.id_soal = oj.id_soal
                            WHERE st.id_soal = ? AND oj.id_opsi = ?
                        ");
                        
                        if ($stmtDetail->execute([$id_soal, $id_opsi])) {
                            $detail = $stmtDetail->fetch(PDO::FETCH_ASSOC);
                            
                            if ($detail) {
                                $hasil_tes['jawaban_detail'][] = [
                                    'id_soal' => $id_soal,
                                    'id_opsi' => $id_opsi,
                                    'pertanyaan' => $detail['pertanyaan'] ?? '',
                                    'opsi' => $detail['opsi'] ?? '',
                                    'bobot' => $detail['bobot'] ?? 0
                                ];
                            }
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Jika masih tidak ditemukan
if (!$hasil_tes) {
    die("Hasil tes tidak ditemukan.");
}

// Validasi kepemilikan
if ($hasil_tes['id_siswa'] != $id_siswa) {
    die("Anda tidak memiliki akses ke hasil tes ini.");
}

// Format tanggal
$tanggal_submit = date("d F Y H:i", strtotime($hasil_tes['tanggal_submit']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil <?= htmlspecialchars($hasil_tes['kategori_tes']) ?> - BK Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: url('../../assets/image/background.jpg') center/cover no-repeat;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            color: #333;
        }
        
        .container-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .main-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 0;
            overflow: hidden;
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        /* Header Info */
        .header-info {
            background: linear-gradient(135deg, #004AAD 0%, #0066cc 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .header-info h4 {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        /* Total Score Box */
        .total-score-box {
            background: #f0f7ff;
            border: 2px solid #004AAD;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }
        
        .total-score-value {
            font-size: 3.5rem;
            font-weight: 800;
            color: #004AAD;
            margin-bottom: 10px;
        }
        
        .total-score-label {
            font-size: 1.2rem;
            color: #6c757d;
        }
        
        /* Info Box */
        .info-box {
            background: #e8f1ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #004AAD;
        }
        
        /* Answer Items */
        .answer-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #004AAD;
            transition: all 0.3s ease;
        }
        
        .answer-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 74, 173, 0.1);
        }
        
        .question-text {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .answer-text {
            color: #495057;
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .bobot-badge {
            background: linear-gradient(135deg, #004AAD 0%, #0066cc 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-top: 4px solid #004AAD;
            height: 100%;
        }
        
        .stat-icon {
            font-size: 2rem;
            color: #004AAD;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #004AAD;
            margin: 10px 0;
        }
        
        /* Teacher Card */
        .teacher-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            border: 2px solid #dee2e6;
        }
        
        .teacher-card h6 {
            color: #004AAD;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        /* Footer */
        .main-footer {
            background: #f8fafd;
            padding: 25px;
            text-align: center;
            color: #6c757d;
            font-size: 0.95rem;
            border-top: 1px solid #e9ecef;
            margin-top: 40px;
        }
        
        /* Buttons */
        .btn-back {
            background: linear-gradient(135deg, #004AAD 0%, #0066cc 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 74, 173, 0.3);
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .header-info {
                padding: 20px;
            }
            
            .total-score-value {
                font-size: 2.8rem;
            }
            
            .answer-item {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="main-container">
            <div class="content-wrapper">
                
                <!-- Header Info -->
                <div class="header-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4><i class="fas fa-chart-line me-2"></i>Hasil Tes <?= htmlspecialchars($hasil_tes['kategori_tes']) ?></h4>
                            <div class="d-flex flex-wrap gap-4">
                                <span><i class="fas fa-user-graduate me-1"></i> <?= htmlspecialchars($nama_siswa) ?></span>
                                <span><i class="fas fa-users me-1"></i> Kelas: <?= htmlspecialchars($kelas_siswa) ?></span>
                                <span><i class="fas fa-calendar-alt me-1"></i> <?= $tanggal_submit ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-light text-dark p-2">
                                <i class="fas fa-clipboard-check me-1"></i> ID: <?= $id_hasil ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="info-box">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3" style="color: #004AAD;"></i>
                        <div>
                            <h6 class="mb-2" style="color: #004AAD;">Informasi Hasil Tes</h6>
                            <p class="mb-0 small">
                                Tes ini adalah <strong>tes bakat/kepribadian</strong> yang tidak memiliki jawaban benar atau salah. 
                                Hasil berupa <strong>total bobot jawaban</strong> akan digunakan oleh guru BK untuk analisis lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Bobot -->
                <div class="total-score-box">
                    <div class="total-score-value"><?= number_format($hasil_tes['nilai'], 1) ?></div>
                    <div class="total-score-label mt-2">Total Bobot Jawaban</div>
                    <div class="mt-3">
                        <span class="bobot-badge fs-6 p-2">
                            <i class="fas fa-calculator me-2"></i>Untuk Analisis Guru BK
                        </span>
                    </div>
                    <p class="text-muted mt-3 small">
                        * Total bobot ini akan dianalisis oleh Guru BK untuk memberikan pemahaman tentang bakat/kepribadian Anda.
                    </p>
                </div>
                
                <!-- Detail Jawaban -->
                <h4 class="mt-5 mb-4" style="color: #004AAD;">
                    <i class="fas fa-list-alt me-2"></i>Detail Jawaban
                    <?php if (isset($hasil_tes['jawaban_detail'])): ?>
                        <span class="badge bg-primary ms-2"><?= count($hasil_tes['jawaban_detail']) ?> Soal</span>
                    <?php endif; ?>
                </h4>
                
                <?php if (!empty($hasil_tes['jawaban_detail'])): ?>
                    <?php foreach ($hasil_tes['jawaban_detail'] as $index => $detail): ?>
                        <div class="answer-item">
                            <div class="row">
                                <div class="col-md-1 text-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; font-weight: bold;">
                                        <?= $index + 1 ?>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="question-text">
                                        <?= htmlspecialchars($detail['pertanyaan']) ?>
                                    </div>
                                    <div class="answer-text mt-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        <?= htmlspecialchars($detail['opsi']) ?>
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <div class="mt-3 mt-md-0">
                                        <div class="bobot-badge d-inline-block">
                                            <i class="fas fa-weight-hanging me-1"></i>
                                            Bobot: <?= number_format($detail['bobot'], 1) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Ringkasan Statistik -->
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-list-ol"></i>
                                </div>
                                <div class="stat-value"><?= count($hasil_tes['jawaban_detail']) ?></div>
                                <div class="text-muted">Jumlah Soal</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="stat-value"><?= number_format($hasil_tes['nilai'], 1) ?></div>
                                <div class="text-muted">Total Bobot</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="stat-value">
                                    <?php 
                                    $jumlah_soal = count($hasil_tes['jawaban_detail']);
                                    echo $jumlah_soal > 0 ? number_format($hasil_tes['nilai'] / $jumlah_soal, 2) : '0.00';
                                    ?>
                                </div>
                                <div class="text-muted">Rata-rata Bobot</div>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>Detail Jawaban Tidak Tersedia</h5>
                        <p class="mb-0">Data detail jawaban tidak dapat ditampilkan.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Catatan untuk Guru BK -->
                <div class="teacher-card">
                    <h6><i class="fas fa-user-tie me-2"></i>Data untuk Analisis Guru BK</h6>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong style="color: #004AAD;">Data Siswa:</strong>
                                <div class="mt-2">
                                    <div><i class="fas fa-user me-2"></i> <?= htmlspecialchars($nama_siswa) ?></div>
                                    <div><i class="fas fa-users me-2"></i> Kelas: <?= htmlspecialchars($kelas_siswa) ?></div>
                                    <div><i class="fas fa-id-card me-2"></i> ID: <?= $id_siswa ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong style="color: #004AAD;">Data Tes:</strong>
                                <div class="mt-2">
                                    <div><i class="fas fa-clipboard-list me-2"></i> <?= htmlspecialchars($hasil_tes['kategori_tes']) ?></div>
                                    <div><i class="fas fa-calculator me-2"></i> Total Bobot: <?= number_format($hasil_tes['nilai'], 1) ?></div>
                                    <div><i class="fas fa-calendar me-2"></i> Tanggal: <?= $tanggal_submit ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <a href="tesbk.php" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Tes
                        </a>
                    </div>
                    <!-- Tombol print bisa diaktifkan jika diperlukan -->
                    <!--
                    <div>
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print me-2"></i> Cetak Hasil
                        </button>
                    </div>
                    -->
                </div>
                
                <!-- Footer Info -->
                <div class="text-center mt-5 text-muted small">
                    <p>
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Hasil ini bersifat indikatif. Analisis lengkap akan diberikan oleh Guru BK melalui konseling.
                    </p>
                </div>
                
            </div>
            
            <!-- Footer -->
            <div class="main-footer">
                <?php
                include 'footer.php';
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Tampilkan notifikasi jika ada
    <?php if (isset($_SESSION['success'])): ?>
        alert('<?= addslashes($_SESSION['success']) ?>');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        alert('Error: <?= addslashes($_SESSION['error']) ?>');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    </script>
</body>
</html>