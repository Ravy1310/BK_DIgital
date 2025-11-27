<?php
$base_dir = $_SERVER['DOCUMENT_ROOT'] . '/BK_DIGITAL/';
require_once $base_dir . 'includes/db_connection.php';

// Inisialisasi variabel
$jumlahGuru = 0;
$akunAktif = 0;
$akunNonaktif = 0;
$dataGuru = [];
$keyword = $_GET['cari'] ?? '';

if ($pdo) {
    try {
        // Query data guru
        if (!empty($keyword)) {
            $sql = "SELECT g.*, u.username, u.email 
                    FROM guru g 
                    LEFT JOIN users u ON g.id_guru = u.id_guru 
                    WHERE (g.nama LIKE ? OR g.telepon LIKE ?)
                    ORDER BY g.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$keyword%", "%$keyword%"]);
        } else {
            $sql = "SELECT g.*, u.username, u.email 
                    FROM guru g 
                    LEFT JOIN users u ON g.id_guru = u.id_guru 
                    ORDER BY g.nama";
            $stmt = $pdo->query($sql);
        }
        
        $dataGuru = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung statistik
        $sqlJumlah = "SELECT COUNT(*) as total FROM guru";
        $stmtJumlah = $pdo->query($sqlJumlah);
        $jumlahGuru = $stmtJumlah->fetch()['total'];

        $sqlAktif = "SELECT COUNT(*) as aktif FROM guru WHERE status = 'Aktif'";
        $stmtAktif = $pdo->query($sqlAktif);
        $akunAktif = $stmtAktif->fetch()['aktif'];

        $sqlNonaktif = "SELECT COUNT(*) as nonaktif FROM guru WHERE status = 'Nonaktif'";
        $stmtNonaktif = $pdo->query($sqlNonaktif);
        $akunNonaktif = $stmtNonaktif->fetch()['nonaktif'];

    } catch (PDOException $e) {
        error_log("Error fetching data: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Data Guru - BK Digital</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../../assets/image/background.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      margin-top: 50px !important;
    }

    h4 {
      font-weight: 700;
      color: #004AAD;
    }

    .stat-card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
      background: white;
      transition: all 0.2s ease;
      border: 1px solid transparent;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-color: rgba(0, 74, 173, 0.1);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      transition: transform 0.2s ease;
    }

    .stat-card:hover .stat-icon {
      transform: scale(1.05);
    }

    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      padding: 24px;
      margin-top: 20px;
    }

    .btn-tambah {
      background-color: #0050BC;
      color: white;
      border: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .btn-tambah:hover {
      background-color: #003580;
      color: white;
      transform: translateY(-1px);
    }

    .search-container { 
      display: flex; 
      align-items: center; 
      gap: 8px; 
    }

    .search-box {
      width: 280px; 
      background: white; 
      border-radius: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0; 
      padding: 10px 20px; 
      font-size: 14px;
      outline: none; 
      transition: all 0.2s ease;
    }

    .search-box:focus {
      border-color: #38A169;
      box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.1);
    }

    .btn-cari {
      background-color: #38A169; 
      border: none; 
      border-radius: 50%;
      width: 44px; 
      height: 44px; 
      display: flex; 
      align-items: center;
      justify-content: center; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      transition: all 0.2s ease;
      color: white;
    }

    .btn-cari:hover { 
      background-color: #2F855A; 
      transform: scale(1.05);
    }

    table { 
      font-size: 0.9rem; 
      margin-bottom: 0;
    }

    .table th {
      background: #f8f9fa;
      font-weight: 600;
      color: #2d3748;
      border-bottom: 2px solid #e2e8f0;
      padding: 12px 8px;
    }

    .table tbody tr {
      transition: background-color 0.2s ease;
      border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
      background-color: #f8fafc;
    }

    .modal-content {
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      border: none;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .modal-header-custom {
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #eee;
      padding: 20px 24px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .modal-title-custom {
      font-weight: 600;
      color: #004AAD;
      margin: 0;
      font-size: 1.3rem;
    }

    .btn-close-custom {
      background: none;
      border: none;
      font-size: 18px;
      color: #666;
      transition: color 0.2s ease;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
    }

    .btn-close-custom:hover { 
      color: #d11a2a;
      background: #f8f9fa;
    }

    .btn-primary {
      background-color: #004AAD;
      border: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .btn-primary:hover {
      background-color: #003580;
      transform: translateY(-1px);
    }

    .status-badge {
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-aktif {
      background-color: #d1fae5;
      color: #065f46;
    }

    .status-nonaktif {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .action-buttons {
      display: flex;
      gap: 6px;
      justify-content: center;
    }

    .btn-action {
      padding: 6px 10px;
      border: none;
      background: none;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .btn-edit {
      color: #004AAD;
    }
    .btn-edit:hover {
      background-color: #e3f2fd;
    }

    .btn-delete {
      color: #dc3545;
    }
    .btn-delete:hover {
      background-color: #fde8e8;
    }

    .btn-status {
      color: #38A169;
    }
    .btn-status:hover {
      background-color: #e8f5e8;
    }

    .password-match {
      border-color: #38A169 !important;
      box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.1) !important;
    }

    .password-mismatch {
      border-color: #dc3545 !important;
      box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1) !important;
    }

    .password-feedback {
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    .password-valid {
      color: #38A169;
    }

    .password-invalid {
      color: #dc3545;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary text-white">
              <i class="bi bi-person-video3 fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Jumlah Guru</h6>
              <h4 class="fw-bold mt-1" id="jumlahGuru"><?= $jumlahGuru ?></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-success text-white">
              <i class="bi bi-check-circle-fill fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Akun Aktif</h6>
              <h4 class="fw-bold mt-1" id="akunAktif"><?= $akunAktif ?></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="card stat-card">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-danger text-white">
              <i class="bi bi-x-circle-fill fs-2"></i>
            </div>
            <div>
              <h6 class="mb-0 text-muted">Akun Nonaktif</h6>
              <h4 class="fw-bold mt-1" id="akunNonaktif"><?= $akunNonaktif ?></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-container">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-2" style="font-size: 1.1rem;">Kelola Data Guru</h6>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <div class="search-container">
            <input type="text" id="searchBox" class="search-box" placeholder="Cari Nama/Telepon Guru" value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn-cari" id="btnCari"><i class="bi bi-search"></i></button>
          </div>

         <button class="btn btn-tambah btn-sm" id="btnTambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg"></i> Tambah Data
</button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle" id="tabelGuru">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>No. Telepon</th>
              <th>Alamat</th>
              <th>Status Akun</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php if (empty($dataGuru)): ?>
              <tr>
                <td colspan="6" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                    <span>Belum ada data guru</span>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($dataGuru as $guru): ?>
                <tr>
                  <td class="fw-medium"><?= $no++ ?></td>
                  <td class="fw-medium"><?= htmlspecialchars($guru['nama']) ?></td>
                  <td><?= htmlspecialchars($guru['telepon']) ?></td>
                  <td><?= htmlspecialchars($guru['alamat']) ?></td>
                  <td>
                    <span class="status-badge <?= $guru['status'] == 'Aktif' ? 'status-aktif' : 'status-nonaktif' ?>">
                      <?= $guru['status'] ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-action btn-edit edit-btn" 
                              data-id="<?= $guru['id_guru'] ?>"
                              data-username="<?= $guru['username'] ?? '' ?>"
                              data-email="<?= $guru['email'] ?? '' ?>">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="btn-action btn-delete delete-btn" data-id="<?= $guru['id_guru'] ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                      <button class="btn-action btn-status status-btn" 
                              data-id="<?= $guru['id_guru'] ?>" 
                              data-status="<?= $guru['status'] ?>">
                        <i class="bi bi-<?= $guru['status'] == 'Aktif' ? 'x' : 'check' ?>-circle"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header-custom">
          <h5 class="modal-title-custom" id="modalTitle">Tambah Data Guru</h5>
          <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
          <form id="formTambah">
            <input type="hidden" id="editId" name="id_guru">
            
            <div class="mb-3">
              <label for="namaGuru" class="form-label">Nama Guru *</label>
              <input type="text" class="form-control" id="namaGuru" name="nama_guru" required>
            </div>
            
            <div class="mb-3">
              <label for="teleponGuru" class="form-label">Telepon *</label>
              <input type="text" class="form-control" id="teleponGuru" name="telepon_guru" required>
            </div>
            
            <div class="mb-3">
              <label for="alamatGuru" class="form-label">Alamat *</label>
              <textarea class="form-control" id="alamatGuru" name="alamat_guru" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
              <label for="username" class="form-label">Username *</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password *</label>
              <input type="password" class="form-control" id="password" name="password" >
              <small class="form-text text-muted" id="passwordHelp">Minimal 6 karakter</small>
            </div>

            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Konfirmasi Password *</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirm_password" >
              <div class="password-feedback" id="passwordFeedback"></div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Data</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Fungsi untuk validasi konfirmasi password
    function validatePassword() {
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirmPassword');
      const feedback = document.getElementById('passwordFeedback');
      const submitBtn = document.getElementById('submitBtn');
      const isEditMode = document.getElementById('editId').value !== '';

      console.log('Validating password - Edit Mode:', isEditMode, 'Password:', password.value);

      // LOGIKA UTAMA: Jika mode edit dan password kosong, skip validasi
      if (isEditMode && password.value === '') {
        confirmPassword.required = false;
        confirmPassword.classList.remove('password-match', 'password-mismatch');
        feedback.textContent = 'Biarkan kosong jika tidak ingin mengubah password';
        feedback.className = 'password-feedback text-muted';
        submitBtn.disabled = false;
        return true;
      }

      // Jika mode edit dan password diisi, atau mode tambah
      confirmPassword.required = true;

      // Validasi panjang password
      if (password.value !== '' && password.value.length < 6) {
        password.classList.add('password-mismatch');
        confirmPassword.classList.remove('password-match', 'password-mismatch');
        feedback.textContent = 'Password harus minimal 6 karakter';
        feedback.className = 'password-feedback password-invalid';
        submitBtn.disabled = true;
        return false;
      } else {
        password.classList.remove('password-mismatch');
      }

      // Validasi konfirmasi password
      if (confirmPassword.value === '') {
        confirmPassword.classList.remove('password-match', 'password-mismatch');
        feedback.textContent = isEditMode ? 'Harap konfirmasi password baru Anda' : 'Harap konfirmasi password Anda';
        feedback.className = 'password-feedback password-invalid';
        submitBtn.disabled = true;
        return false;
      }

      // Validasi kecocokan password
      if (password.value === confirmPassword.value) {
        confirmPassword.classList.add('password-match');
        confirmPassword.classList.remove('password-mismatch');
        feedback.textContent = 'Password cocok';
        feedback.className = 'password-feedback password-valid';
        submitBtn.disabled = false;
        return true;
      } else {
        confirmPassword.classList.add('password-mismatch');
        confirmPassword.classList.remove('password-match');
        feedback.textContent = 'Password tidak cocok';
        feedback.className = 'password-feedback password-invalid';
        submitBtn.disabled = true;
        return false;
      }
    }

    // Fungsi untuk setup mode edit - DIPANGGIL DARI JavaScript external
    function setupEditMode() {
      const editId = document.getElementById('editId');
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirmPassword');
      const passwordHelp = document.getElementById('passwordHelp');
      const feedback = document.getElementById('passwordFeedback');

      console.log('Setting up edit mode, ID:', editId.value);

      if (editId.value !== '') {
        // Mode edit - Password opsional
        password.required = false;
        confirmPassword.required = false;
        passwordHelp.textContent = 'Kosongkan jika tidak ingin mengubah password';
        
        // Reset field konfirmasi password
        confirmPassword.value = '';
        
        // Reset validasi UI
        password.classList.remove('password-match', 'password-mismatch');
        confirmPassword.classList.remove('password-match', 'password-mismatch');
        feedback.textContent = 'Biarkan kosong jika tidak ingin mengubah password';
        feedback.className = 'password-feedback text-muted';
        
        // Enable submit button
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      } else {
        // Mode tambah - Password wajib
        password.required = true;
        confirmPassword.required = true;
        passwordHelp.textContent = 'Minimal 6 karakter';
        feedback.textContent = '';
        feedback.className = 'password-feedback';
      }
      
      // Jalankan validasi setelah perubahan mode
      setTimeout(validatePassword, 100);
    }

    // Event listeners untuk validasi real-time
    document.addEventListener('DOMContentLoaded', function() {
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirmPassword');

      if (password && confirmPassword) {
        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);
      }

      // Inisialisasi modal
      const modalElement = document.getElementById('modalTambah');
      if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function() {
          // Reset validasi saat modal dibuka
          setTimeout(validatePassword, 100);
        });

        modalElement.addEventListener('hidden.bs.modal', function() {
          // Reset form saat modal ditutup
          const form = document.getElementById('formTambah');
          if (form) {
            form.reset();
            document.getElementById('editId').value = '';
            document.getElementById('modalTitle').textContent = "Tambah Data Guru";
            
            // Reset ke mode tambah
            setupEditMode();
          }
        });
      }
    });

    // Export fungsi ke global scope untuk dipanggil dari JavaScript external
    window.validatePassword = validatePassword;
    window.setupEditMode = setupEditMode;
  </script>
  </body>
</html>