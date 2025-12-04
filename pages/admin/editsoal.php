<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

// CEK ROLE
if ($_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: ../../login.php?error=unauthorized");
    exit;
}

// GET PARAMETER
$id_tes = isset($_GET['id_tes']) ? intval($_GET['id_tes']) : 0;

require_once __DIR__ . '/../../includes/db_connection.php';


// CEK TES ADA
$stmt_tes = $pdo->prepare("SELECT kategori_tes, deskripsi_tes FROM tes WHERE id_tes = ?");
$stmt_tes->execute([$id_tes]);
$tesData = $stmt_tes->fetch(PDO::FETCH_ASSOC);

if (!$tesData) {
    die("Tes tidak ditemukan");
}

$tes = $tesData['kategori_tes']; 

// AMBIL SEMUA SOAL TES
$stmt_soal = $pdo->prepare("SELECT * FROM soal_tes WHERE id_tes = ? ORDER BY id_soal ASC");
$stmt_soal->execute([$id_tes]);
$soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

$total_soal = count($soal_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Soal | <?= htmlspecialchars($tes) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 20px 10px;
      min-height: 100vh;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
    }
    .btn-merah {
      background-color: #C60000 !important;
      color: #fff !important;
      border: none !important;
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-merah:hover {
      background-color: #710303 !important;
      transform: translateY(-2px);
    }
    .btn-primary {
      background-color: #0066cc;
      border: none;
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #0052a3;
      transform: translateY(-2px);
    }
    .btn-success {
      border-radius: 8px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    .btn-success:hover {
      transform: translateY(-2px);
    }
    .soal-card {
      background: #ffffff;
      border: 1px solid #e0e0e0;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }
    .soal-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }
    .soal-number {
      background: #0066cc;
      color: white;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-right: 15px;
      flex-shrink: 0;
    }
    .opsi-jawaban {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-top: 10px;
    }
    .opsi-item {
      padding: 5px 0;
      border-bottom: 1px solid #e9ecef;
    }
    .opsi-item:last-child {
      border-bottom: none;
    }
    .header-section {
      border-bottom: 2px solid #e9ecef;
      padding-bottom: 15px;
      margin-bottom: 25px;
    }
    .action-buttons {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 15px;
    }
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #6c757d;
    }
    .empty-state i {
      font-size: 48px;
      margin-bottom: 15px;
      color: #dee2e6;
    }
    .badge-count {
      background: #0066cc;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.875rem;
    }
    
    /* CUSTOM MODAL STYLES */
    .custom-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }
    .custom-modal.show {
      display: flex !important;
    }
    .custom-modal-content {
      background: white;
      border-radius: 16px;
      width: 90%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .custom-modal-header {
      padding: 20px;
      border-bottom: 2px solid #e9ecef;
      background: #f8f9fa;
      border-radius: 16px 16px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .custom-modal-body {
      padding: 20px;
    }
    .custom-modal-footer {
      padding: 20px;
      border-top: 2px solid #e9ecef;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .custom-modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #6c757d;
    }
    .custom-modal-close:hover {
      color: #000;
    }
    
    /* Custom Alert Styles */
    .custom-alert {
      border-radius: 12px;
      border: none;
      font-family: 'Poppins', sans-serif;
    }
    .alert-success {
      background: #d4edda;
      color: #155724;
      border-left: 4px solid #28a745;
    }
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border-left: 4px solid #dc3545;
    }
    
    /* Form Styles */
    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 10px 15px;
    }
    .form-control:focus, .form-select:focus {
      border-color: #0066cc;
      box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }
    /* Styling untuk form tambah soal dalam modal */
.opsi-item {
    transition: all 0.3s ease;
}

.opsi-item:hover {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.btn-danger.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Pastikan modal backdrop bekerja dengan baik */
.custom-modal {
    backdrop-filter: blur(5px);
}
  </style>
</head>

<body>

<div class="container my-4">
  <div class="card p-4">

    <!-- HEADER SECTION -->
    <div class="header-section">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="fw-bold mb-1 text-dark">Kelola Soal: <?= htmlspecialchars($tes) ?></h4>
          <p class="text-muted mb-0"><?= htmlspecialchars($tesData['deskripsi_tes']) ?></p>
        </div>
        
        <!-- BUTTON TAMBAH SOAL -->
        <button class="btn btn-primary px-4" id="btnTambahSoal">
          <i class="fas fa-plus me-2"></i>Tambah Soal Baru
        </button>
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Daftar soal untuk tes ini.</p>
        <?php if ($total_soal > 0): ?>
          <span class="badge-count">Total: <?= $total_soal ?> soal</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($total_soal === 0): ?>
      <!-- EMPTY STATE -->
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h5 class="text-muted">Belum ada soal</h5>
        <p class="text-muted">Mulai dengan menambahkan soal pertama untuk tes ini.</p>
        <button class="btn btn-primary px-4 mt-2" id="btnTambahPertama">
          <i class="fas fa-plus me-2"></i>Tambah Soal Pertama
        </button>
      </div>
    <?php else: ?>
      <!-- LIST SOAL -->
      <div class="soal-container">
        <?php
        $no = 1;
        foreach ($soal_list as $s):
            // AMBIL OPSI DARI TABEL opsi_jawaban untuk tampilan card
            $stmt_opsi = $pdo->prepare("SELECT * FROM opsi_jawaban WHERE id_soal = ? ORDER BY id_opsi ASC");
            $stmt_opsi->execute([$s['id_soal']]);
            $opsi_list = $stmt_opsi->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <div class="soal-card">
          <div class="d-flex align-items-start mb-3">
            <div class="soal-number"><?= $no ?></div>
            <div class="flex-grow-1">
              <h6 class="fw-bold mb-2 text-dark">Pertanyaan:</h6>
              <p class="mb-3"><?= nl2br(htmlspecialchars($s['pertanyaan'])) ?></p>
              
              <!-- OPSI JAWABAN -->
              <div class="opsi-jawaban">
                <h6 class="fw-bold mb-3 text-dark">Opsi Jawaban:</h6>
                <?php if (count($opsi_list) > 0): ?>
                  <div class="opsi-list">
                    <?php foreach ($opsi_list as $i => $o): 
                      $huruf = chr(65 + $i); // A, B, C ...
                    ?>
                      <div class="opsi-item">
                        <strong class="text-primary"><?= $huruf ?>.</strong> 
                        <?= htmlspecialchars($o['opsi']) ?> 
                        <small class="text-muted">(Bobot: <?= $o['bobot'] ?>)</small>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="text-muted fst-italic">
                    <i class="fas fa-exclamation-triangle me-2"></i>Belum ada opsi jawaban
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- ACTION BUTTONS -->
          <div class="action-buttons">
            <button class="btn btn-success px-3 btn-edit-soal" 
                    data-soal-id="<?= $s['id_soal'] ?>" 
                    data-tes-id="<?= $id_tes ?>" 
                    data-nomor="<?= $no ?>">
              <i class="fas fa-edit me-1"></i>Edit
            </button>
            <button class="btn btn-merah px-3 btn-hapus-soal" 
                    data-soal-id="<?= $s['id_soal'] ?>" 
                    data-tes-id="<?= $id_tes ?>">
              <i class="fas fa-trash me-1"></i>Hapus
            </button>
          </div>
        </div>
        
        <?php 
        $no++;
        endforeach; 
        ?>
      </div>
    <?php endif; ?>

    <!-- FOOTER BUTTONS -->
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
      <button type="button" class="btn btn-outline-secondary px-4" id="btnKembali">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Kelola Soal
      </button>
      
      <?php if ($total_soal > 0): ?>
        <div class="text-muted small">
          Total: <strong><?= $total_soal ?> soal</strong>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- CUSTOM MODAL UNTUK EDIT SOAL -->
<div id="editModal" class="custom-modal">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5 class="modal-title fw-bold" id="editModalTitle">
        <i class="fas fa-edit me-2"></i>Edit Soal
      </h5>
      <button type="button" class="custom-modal-close" id="btnCloseModal">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="custom-modal-body" id="editModalBody">
      <!-- Konten form edit akan diisi via JavaScript -->
    </div>
  </div>
</div>

<!-- FORM EDIT SOAL (HIDDEN - AKAN DICOPY KE MODAL) -->
<div id="editSoalTemplate" style="display: none;">
  <form class="edit-soal-form" method="POST">
    <input type="hidden" name="id_soal" id="edit_id_soal">
    <input type="hidden" name="id_tes" id="edit_id_tes">

    <div class="mb-4">
        <label class="form-label fw-semibold">Pertanyaan</label>
        <textarea class="form-control" name="pertanyaan" rows="4" required style="min-height: 120px;" id="edit_pertanyaan"></textarea>
    </div>

    <label class="form-label fw-semibold mb-3">Pilihan Jawaban:</label>

    <div class="card mb-3 border">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Opsi A</label>
                    <input type="text" class="form-control" name="opsi_a" placeholder="Masukkan opsi A" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bobot A</label>
                    <select class="form-select" name="bobot_a" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <small class="text-muted">Nilai: 1-5</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 border">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Opsi B</label>
                    <input type="text" class="form-control" name="opsi_b" placeholder="Masukkan opsi B" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bobot B</label>
                    <select class="form-select" name="bobot_b" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <small class="text-muted">Nilai: 1-5</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 border">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Opsi C</label>
                    <input type="text" class="form-control" name="opsi_c" placeholder="Masukkan opsi C" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bobot C</label>
                    <select class="form-select" name="bobot_c" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <small class="text-muted">Nilai: 1-5</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 border">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Opsi D</label>
                    <input type="text" class="form-control" name="opsi_d" placeholder="Masukkan opsi D" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bobot D</label>
                    <select class="form-select" name="bobot_d" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <small class="text-muted">Nilai: 1-5</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 border">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-medium">Opsi E</label>
                    <input type="text" class="form-control" name="opsi_e" placeholder="Masukkan opsi E" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Bobot E</label>
                    <select class="form-select" name="bobot_e" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <small class="text-muted">Nilai: 1-5</small>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-modal-footer">
        <button type="button" class="btn btn-secondary px-4" onclick="editSoalManager.hideModal()">
            <i class="fas fa-times me-2"></i>Batal
        </button>
        <button type="submit" class="btn btn-success px-4">
            <i class="fas fa-save me-2"></i>Simpan Perubahan
        </button>
    </div>
  </form>
</div>
<!-- MODAL TAMBAH SOAL -->
<!-- MODAL TAMBAH SOAL -->
<div id="tambahModal" class="custom-modal">
  <div class="custom-modal-content">
    <div class="custom-modal-header">
      <h5 class="modal-title fw-bold">
        <i class="fas fa-plus me-2"></i>Tambah Soal Baru
      </h5>
      <button type="button" class="custom-modal-close" onclick="window.tambahSoalManager.hideModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <form id="tambahSoalForm" method="POST">
        <input type="hidden" name="id_tes" value="<?= $id_tes ?>">
        
        <div class="mb-4">
          <label class="form-label fw-semibold">Pertanyaan</label>
          <textarea name="pertanyaan" class="form-control" rows="4" required 
                    placeholder="Masukkan pertanyaan soal" style="min-height: 120px;"></textarea>
        </div>

        <label class="form-label fw-semibold mb-3">Opsi Jawaban + Bobot:</label>
        
        <div id="opsi-wrapper" class="mb-3">
          <!-- Opsi akan ditambahkan secara dinamis -->
          <div class="opsi-item mb-3">
            <div class="row align-items-center">
              <div class="col-md-8">
                <input type="text" name="opsi[]" class="form-control" placeholder="Opsi jawaban" required>
              </div>
              <div class="col-md-3">
                <input type="number" name="bobot[]" class="form-control" min="1" max="10" value="1" required>
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="window.tambahSoalManager.hapusOpsi(this)" disabled>
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          
          <div class="opsi-item mb-3">
            <div class="row align-items-center">
              <div class="col-md-8">
                <input type="text" name="opsi[]" class="form-control" placeholder="Opsi jawaban" required>
              </div>
              <div class="col-md-3">
                <input type="number" name="bobot[]" class="form-control" min="1" max="10" value="1" required>
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="window.tambahSoalManager.hapusOpsi(this)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-primary mb-3" onclick="window.tambahSoalManager.tambahOpsi()">
          <i class="fas fa-plus me-2"></i>Tambah Opsi
        </button>

        <div class="custom-modal-footer">
          <button type="button" class="btn btn-secondary px-4" onclick="window.tambahSoalManager.hideModal()">
            <i class="fas fa-times me-2"></i>Batal
          </button>
          <button type="submit" class="btn btn-success px-4">
            <i class="fas fa-save me-2"></i>Simpan Soal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Di akhir file editsoal.php, sebelum </body> -->
<script>
// Pastikan ID Tes tersimpan dengan baik
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const idTes = urlParams.get('id_tes');
    
    if (idTes) {
        // Simpan ID Tes untuk backup
        sessionStorage.setItem('current_id_tes', idTes);
        localStorage.setItem('current_id_tes', idTes);
        console.log('💾 ID Tes disimpan:', idTes);
    }
    
    // Inisialisasi managers dengan delay untuk memastikan DOM siap
    setTimeout(() => {
        if (typeof initEditSoalManager === 'function') {
            initEditSoalManager();
        }
        if (typeof initTambahSoalManager === 'function') {
            initTambahSoalManager();
        }
        if (typeof setupManualEventHandlers === 'function') {
            setupManualEventHandlers();
        }
    }, 300);
});

// Fallback initialization
setTimeout(() => {
    if (!window.editSoalManager) {
        console.log('🔄 Fallback: Initializing EditSoalManager...');
        if (typeof initEditSoalManager === 'function') {
            initEditSoalManager();
        }
    }
    if (!window.tambahSoalManager) {
        console.log('🔄 Fallback: Initializing TambahSoalManager...');
        if (typeof initTambahSoalManager === 'function') {
            initTambahSoalManager();
        }
    }
}, 1000);

// Debug info
console.log('🔧 Current ID Tes from URL:', new URLSearchParams(window.location.search).get('id_tes'));
</script>
</body>
</html>