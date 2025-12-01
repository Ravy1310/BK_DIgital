<?php 
session_start();
require_once __DIR__ . "/../../includes/db_connection.php";

// Validasi session verifikasi
require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";
validateAndRedirect('tes');

// Dapatkan data siswa dari session verifikasi
$siswa_data = getCurrentStudent();
$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];
$kelas_siswa = $siswa_data['kelas'];

// Ambil ID tes
$id_tes = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi ID tes
if ($id_tes <= 0) {
    header("Location: tesbk.php");
    exit;
}

// Ambil informasi tes
try {
    $stmt_tes = $pdo->prepare("SELECT * FROM tes WHERE id_tes = ? AND status = 'aktif'");
    $stmt_tes->execute([$id_tes]);
    $tes_info = $stmt_tes->fetch(PDO::FETCH_ASSOC);
    
    if (!$tes_info) {
        $_SESSION['error'] = "Tes tidak ditemukan atau tidak aktif";
        header("Location: tesbk.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error mengambil data tes: " . $e->getMessage();
    header("Location: tesbk.php");
    exit;
}

// Ambil daftar soal berdasarkan id_tes beserta opsi jawaban
try {
    $stmt_soal = $pdo->prepare("
        SELECT s.*, o.id_opsi, o.opsi, o.bobot 
        FROM soal_tes s 
        LEFT JOIN opsi_jawaban o ON s.id_soal = o.id_soal 
        WHERE s.id_tes = ? 
        ORDER BY s.id_soal ASC, o.id_opsi ASC
    ");
    $stmt_soal->execute([$id_tes]);
    $soal_data = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);
    
    // Kelompokkan soal dengan opsi
    $soal_list = [];
    foreach ($soal_data as $row) {
        $soal_id = $row['id_soal'];
        if (!isset($soal_list[$soal_id])) {
            $soal_list[$soal_id] = [
                'id_soal' => $row['id_soal'],
                'pertanyaan' => $row['pertanyaan'],
                'opsi' => []
            ];
        }
        if ($row['id_opsi']) {
            $soal_list[$soal_id]['opsi'][] = [
                'id_opsi' => $row['id_opsi'],
                'opsi' => $row['opsi'],
                'bobot' => $row['bobot']
            ];
        }
    }
    $soal_list = array_values($soal_list); // Reset keys
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error mengambil soal: " . $e->getMessage();
    header("Location: tesbk.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tes_info['kategori_tes']) ?> - BK Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background: url('../../assets/image/background.jpg') center/cover no-repeat;
            font-family: 'Poppins', sans-serif;
            padding-top: 30px;
            padding-bottom: 30px;
        }
        
        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 0 auto;
            padding: 0;
            overflow: hidden;
            max-width: 900px;
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        .header-info {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 5px solid #004AAD;
        }
        
        .header-info h4 {
            color: #004AAD;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .question-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        
        .question-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .option-item {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .option-item:hover {
            border-color: #004AAD;
            background-color: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 74, 173, 0.1);
        }
        
        .option-item.selected {
            border-color: #004AAD;
            background-color: #e3f2fd;
        }
        
        .option-input {
            display: none;
        }
        
        .option-label {
            display: flex;
            align-items: center;
            width: 100%;
            cursor: pointer;
            margin: 0;
        }
        
        .option-text {
            font-size: 16px;
            color: #333;
            flex: 1;
        }
        
        .option-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #adb5bd;
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
            flex-shrink: 0;
        }
        
        .option-item.selected .option-radio {
            border-color: #004AAD;
            background-color: #004AAD;
        }
        
        .option-item.selected .option-radio:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }
        
        .progress-container {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #004AAD, #0066cc);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-cancel {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .question-number {
            background: #004AAD;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .header-info {
                padding: 15px;
            }
            
            .question-card {
                padding: 20px;
            }
            
            .option-item {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

<div class="main-container">
    <div class="content-wrapper">
        <!-- Header Info -->
        <div class="header-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4><i class="fas fa-clipboard-list me-2"></i><?= htmlspecialchars($tes_info['kategori_tes']) ?></h4>
                    <div class="d-flex flex-wrap gap-3 text-muted">
                        <span><i class="fas fa-user-graduate me-1"></i> <?= htmlspecialchars($nama_siswa) ?></span>
                        <span><i class="fas fa-users me-1"></i> Kelas: <?= htmlspecialchars($kelas_siswa) ?></span>
                        <span><i class="fas fa-question-circle me-1"></i> Soal: <?= count($soal_list) ?></span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="tesbk.php" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar" id="progressBar" style="width: 0%"></div>
        </div>
        
        <!-- FORM -->
        <form action="submit_tes.php" method="POST" id="tesForm">
            <!-- Kirim data tersembunyi -->
            <input type="hidden" name="id_tes" value="<?= $id_tes ?>">
            <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">

            <!-- Card Soal -->
            <?php if (count($soal_list) > 0): ?>
                <?php foreach ($soal_list as $index => $soal): ?>
                    <div class="question-card" id="soal-<?= $soal['id_soal'] ?>">
                        <div class="question-title d-flex align-items-center">
                            <div class="question-number"><?= $index + 1 ?></div>
                            <div class="question-text"><?= htmlspecialchars($soal['pertanyaan']) ?></div>
                        </div>
                        
                        <div class="options-container">
                            <?php if (!empty($soal['opsi'])): ?>
                                <?php foreach ($soal['opsi'] as $opsi): ?>
                                    <div class="option-item" onclick="selectOption(this, <?= $soal['id_soal'] ?>, <?= $opsi['id_opsi'] ?>)">
                                        <label class="option-label">
                                            <div class="option-radio"></div>
                                            <div class="option-text"><?= htmlspecialchars($opsi['opsi']) ?></div>
                                            <input type="radio" 
                                                   class="option-input" 
                                                   name="jawaban[<?= $soal['id_soal'] ?>]" 
                                                   value="<?= $opsi['id_opsi'] ?>" 
                                                   data-bobot="<?= $opsi['bobot'] ?>"
                                                   required>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Opsi jawaban belum tersedia untuk soal ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <button type="button" class="btn btn-cancel" onclick="confirmCancel()">
                            <i class="fas fa-times me-2"></i> Batal
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i> Submit Tes
                        </button>
                    </div>
                </div>
                
                <!-- Informasi -->
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Pastikan semua soal telah dijawab sebelum menekan tombol Submit.
                    Anda dapat mengubah jawaban sebelum mengirimkan.
                </div>
                
            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <h5 class="mb-2">Soal belum tersedia</h5>
                    <p class="mb-0">Belum ada soal yang tersedia untuk tes ini.</p>
                    <a href="tesbk.php" class="btn btn-back mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Tes
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<!-- Tambahkan sebelum </body> -->
<div class="modal fade" id="warningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Masih ada <span id="unansweredCount" class="fw-bold">0</span> soal yang belum dijawab.</p>
                <p>Silakan lengkapi semua soal sebelum mengirim tes.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="scrollToUnanswered()" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-down me-2"></i>Tunjukkan Soal
                </button>
            </div>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fungsi untuk memilih opsi
function selectOption(element, soalId, opsiId) {
    // Hapus selected dari semua opsi dalam soal yang sama
    const questionCard = element.closest('.question-card');
    const allOptions = questionCard.querySelectorAll('.option-item');
    allOptions.forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Tambah selected ke opsi yang dipilih
    element.classList.add('selected');
    
    // Update input radio
    const radioInput = element.querySelector('input[type="radio"]');
    if (radioInput) {
        radioInput.checked = true;
    }
    
    // Update progress
    updateProgress();
}

// Fungsi untuk update progress bar
function updateProgress() {
    const totalQuestions = <?= count($soal_list) ?>;
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (totalQuestions > 0) {
        const progressPercent = (answeredQuestions / totalQuestions) * 100;
        document.getElementById('progressBar').style.width = progressPercent + '%';
    }
}

// Fungsi konfirmasi batal
function confirmCancel() {
    if (confirm('Apakah Anda yakin ingin membatalkan tes? Semua jawaban akan hilang.')) {
        window.location.href = 'tesbk.php';
    }
}
// Di form_tes.php, tambahkan sebelum form submit:
document.getElementById('tesForm').addEventListener('submit', function(e) {
    // Cek semua soal terjawab
    const totalQuestions = <?= count($soal_list) ?>;
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answeredQuestions < totalQuestions) {
        e.preventDefault();
        const unanswered = totalQuestions - answeredQuestions;
        
        // Tampilkan modal atau alert yang lebih baik
        const modal = new bootstrap.Modal(document.getElementById('warningModal'));
        document.getElementById('unansweredCount').textContent = unanswered;
        modal.show();
        
        return false;
    }
    
    // Tampilkan loading
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    submitBtn.disabled = true;
    
    return true;
});

// Fungsi validasi form sebelum submit
document.getElementById('tesForm').addEventListener('submit', function(e) {
    const totalQuestions = <?= count($soal_list) ?>;
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answeredQuestions < totalQuestions) {
        e.preventDefault();
        const unanswered = totalQuestions - answeredQuestions;
        alert(`Masih ada ${unanswered} soal yang belum dijawab. Silakan lengkapi semua soal sebelum mengirim.`);
        return false;
    }
    
    // Tampilkan loading
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    submitBtn.disabled = true;
    
    // Tambahkan timeout untuk mengembalikan tombol jika submit terlalu lama
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            alert('Gagal mengirim tes. Silakan coba lagi.');
        }
    }, 10000);
    
    return true;
});

// Inisialisasi progress bar saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Tambahkan event listener untuk input radio change
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', updateProgress);
    });
    
    // Tambahkan keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Navigasi dengan tombol angka (1-5)
        if (e.key >= '1' && e.key <= '5') {
            const activeQuestion = document.querySelector('.question-card:target') || 
                                 document.querySelector('.question-card:first-child');
            if (activeQuestion) {
                const options = activeQuestion.querySelectorAll('.option-item');
                const optionIndex = parseInt(e.key) - 1;
                if (options[optionIndex]) {
                    const radioInput = options[optionIndex].querySelector('input[type="radio"]');
                    if (radioInput) {
                        selectOption(options[optionIndex], 
                                   radioInput.name.replace('jawaban[', '').replace(']', ''),
                                   radioInput.value);
                    }
                }
            }
        }
    });
});

// Auto scroll ke soal pertama yang belum dijawab
function scrollToUnanswered() {
    const unansweredQuestions = document.querySelectorAll('.question-card');
    for (let question of unansweredQuestions) {
        const hasAnswer = question.querySelector('input[type="radio"]:checked');
        if (!hasAnswer) {
            question.scrollIntoView({ behavior: 'smooth', block: 'center' });
            break;
        }
    }
}
</script>

</body>
</html>