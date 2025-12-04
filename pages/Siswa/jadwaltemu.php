<?php 
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";
validateAndRedirect('jadwal');
$siswa_data = getCurrentStudent();
$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];
$kelas_siswa = $siswa_data['kelas'];
// ======================
// CEK PATH DATABASE CONNECTION
// ======================
$db_path_1 = __DIR__ . "/includes/db_connection.php";
$db_path_2 = __DIR__ . "/../../includes/db_connection.php";

if (file_exists($db_path_1)) {
    require_once $db_path_1;
} elseif (file_exists($db_path_2)) {
    require_once $db_path_2;
} else {
    die("File database connection tidak ditemukan");
}

$student_data = getCurrentStudent();
$id_siswa = $student_data['id_siswa'];

// ======================
// AMBIL DATA SISWA DARI DATABASE
// ======================
$stmt_siswa = $pdo->prepare("
    SELECT s.*
    FROM siswa s 
    WHERE s.id_siswa = ?
");
$stmt_siswa->execute([$id_siswa]);
$siswa_data = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa_data) {
    echo "Data siswa tidak ditemukan";
    exit;
}

// ======================
// PROSES RESCHEDULE JADWAL
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reschedule') {
    $id_jadwal = $_POST['id_jadwal'];
    $tanggal_baru = $_POST['tanggal'];
    $waktu_baru = $_POST['jam'];
    
    // Update jadwal di database - KETERANGAN TIDAK DIUBAH
    $stmt_update = $pdo->prepare("
        UPDATE jadwal_konseling 
        SET Tanggal_Konseling = ?, 
            Waktu_Konseling = ?, 
            Status = 'Menunggu'
        WHERE ID_Jadwal = ? AND id_siswa = ?
    ");
    
    $success = $stmt_update->execute([$tanggal_baru, $waktu_baru, $id_jadwal, $id_siswa]);
    
    if ($success) {
        $_SESSION['toast_success'] = "Jadwal berhasil direschedule! Menunggu persetujuan guru BK.";
        header("Location: jadwaltemu.php");
        exit;
    } else {
        $_SESSION['toast_error'] = "Gagal mereschedule jadwal. Silakan coba lagi.";
    }
}

// ======================
// PROSES PENGAJUAN JADWAL BARU
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $tanggal = $_POST['tanggal'];
    $topik = $_POST['topik'];
    $jam = $_POST['jam'];
    $id_guru = $_POST['id_guru'];
    $keterangan = $_POST['keterangan'] ?? '';
    
    // Insert jadwal baru ke database
    $stmt_insert = $pdo->prepare("
        INSERT INTO jadwal_konseling 
        (id_siswa, id_guru, Tanggal_Konseling, Waktu_Konseling, Topik_Konseling, keterangan, Status) 
        VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')
    ");
    
    $success = $stmt_insert->execute([$id_siswa, $id_guru, $tanggal, $jam, $topik, $keterangan]);
    
    if ($success) {
        $_SESSION['toast_success'] = "Jadwal berhasil diajukan! Menunggu persetujuan guru BK.";
        header("Location: jadwaltemu.php");
        exit;
    } else {
        $_SESSION['toast_error'] = "Gagal mengajukan jadwal. Silakan coba lagi.";
    }
}

// ======================
// AMBIL JADWAL AKTIF SISWA - HANYA STATUS MENUNGGU, DISETUJUI, JADWALKAN ULANG
// ======================
$stmt_jadwal = $pdo->prepare("
    SELECT jk.*, g.nama AS nama_guru
    FROM jadwal_konseling jk
    LEFT JOIN guru g ON jk.id_guru = g.id_guru
    WHERE jk.id_siswa = ? AND jk.Status IN ('Menunggu', 'Jadwalkan Ulang')
    ORDER BY 
        CASE 
            WHEN jk.Status = 'Jadwalkan Ulang' THEN 1
            WHEN jk.Status = 'Menunggu' THEN 2
            ELSE 3
        END,
        jk.Tanggal_Konseling ASC
");
$stmt_jadwal->execute([$id_siswa]);
$jadwal_aktif = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

// ======================
// AMBIL RIWAYAT JADWAL - STATUS DITOLAK DAN SELESAI
// ======================
$stmt_riwayat = $pdo->prepare("
    SELECT jk.*, g.nama AS nama_guru
    FROM jadwal_konseling jk
    LEFT JOIN guru g ON jk.id_guru = g.id_guru
    WHERE jk.id_siswa = ? AND jk.Status IN ('Ditolak', 'Selesai','Disetujui')
    ORDER BY jk.Tanggal_Konseling DESC
");
$stmt_riwayat->execute([$id_siswa]);
$riwayat_jadwal = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Konseling</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  body {
      padding-top: 0px;
      background: url('../../assets/image/background.jpg');
        background-size: cover;
      font-family: 'Poppins', sans-serif;
  }

  /* ===================== CARD KONSELING ===================== */
  .schedule-wrapper {
      background: #ffffff;
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 35px;
      border: 1px solid #dcdcdc;
  }
  .section-title {
      font-size: 28px;
      font-weight: 700;
      text-align: center;
      margin-bottom: 25px;
  }
  .carousel-container {
      position: relative;
      overflow: hidden;
      padding: 5px 0;
      margin: 0 -10px;
  }
  .carousel-wrapper {
      display: flex;
      transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      gap: 20px;
      padding: 10px;
  }
  .konseling-card {
      flex: 0 0 calc(33.333% - 14px);
      background: #fff;
      border-radius: 15px;
      padding: 0;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
      min-width: 0;
      margin: 5px 0;
      transition: all 0.3s ease;
  }
  .konseling-card:hover {
  
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 12px 25px rgba(0,0,0,0.15);
  }
  .konseling-header {
      padding: 15px 18px;
      color: #fff;
      font-weight: 600;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      text-align: center;
      font-size: 16px;
  }
  .konseling-body {
      padding: 20px 18px 25px 18px;
  }
  .btn-konseling {
      background: #0d47a1;
      color: #fff;
      padding: 12px 25px;
      font-weight: 600;
      border-radius: 10px;
      transition: all 0.3s ease;
  }
  .btn-konseling:hover {
      background: #0050BC;
      color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
  }
  .btn-reschedule {
      background: #fd7e14;
      color: #fff;
      padding: 10px 16px;
      font-size: 0.875rem;
      border-radius: 8px;
      margin-top: 12px;
      width: 100%;
      transition: all 0.3s ease;
  }
  .btn-reschedule:hover {
      background: #e8590c;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(253, 126, 20, 0.3);
  }
  /* WARNA STATUS */
  .status-Menunggu { background: #ffc107; color: #000; }
  .status-Disetujui { background: #198754; color: #fff; }
  .status-Jadwalkan-Ulang { 
      background: #0d6efd;
      color: #fff; 
  }
  .status-Ditolak { background: #dc3545; color: #fff; }
  .status-Selesai { background: #6c757d; color: #fff; }
  .urgent-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #dc3545;
      color: white;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: bold;
      animation: pulse 2s infinite;
      z-index: 5;
  }
  @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
  }
  /* TOAST NOTIFICATION */
  .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
  }
  .toast {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-left: 4px solid;
  }
  .toast-success {
      border-left-color: #198754;
  }
  .toast-error {
      border-left-color: #dc3545;
  }
  /* CAROUSEL CONTROLS */
  .carousel-control {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(13, 71, 161, 0.8);
      color: white;
      border: none;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }
  .carousel-control.prev {
      left: 15px;
  }
  .carousel-control.next {
      right: 15px;
  }
  .carousel-control:hover {
      background: #0d47a1;
      transform: translateY(-50%) scale(1.1);
      box-shadow: 0 6px 18px rgba(0,0,0,0.3);
  }
  .carousel-control.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
  }
  /* ANIMASI SLIDE */
  .carousel-wrapper.sliding {
      transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  }
  /* CARD CONTENT STYLING */
  .konseling-body p {
      margin-bottom: 8px;
      line-height: 1.4;
  }
  .konseling-body .fw-semibold {
      font-size: 1.1rem;
      color: #2c3e50;
  }
  .konseling-body small {
      font-size: 0.85rem;
      color: #6c757d;
  }
  .user-info {
      background-color: #cedaefff;
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 40px;
      border-left: 5px solid #004AAD;
    }

    .user-info h5 {
      color: #004AAD;
      font-weight: 700;
      margin-bottom: 8px;
      font-size: 1.4rem;
    }
</style>
</head>

<body>

<div class="container py-4">
  

 

  <!-- JADWAL AKTIF -->

  <div class="schedule-wrapper">
    <div class="section-title">Jadwal Konseling Aktif</div>
     <!-- Info Siswa -->
        <div class="user-info">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h5><i class="fas fa-user-graduate me-2"></i><?= htmlspecialchars($nama_siswa) ?></h5>
            <div class="d-flex flex-wrap gap-4 text-muted mt-2">
              <span><i class="fas fa-id-card me-1"></i> ID: <?= htmlspecialchars($id_siswa) ?></span>
              <span><i class="fas fa-users me-1"></i> Kelas: <?= htmlspecialchars($kelas_siswa) ?></span>
              <span><i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y') ?></span>
            </div>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0">
            
          </div>
        </div>
      </div>

    <?php if (count($jadwal_aktif) > 0): ?>
      <div class="carousel-container">
        <button class="carousel-control prev" onclick="slideCarousel('aktif', -1)">
          <i class="bi bi-chevron-left"></i>
        </button>
        
        <div class="carousel-wrapper" id="carouselAktif">
          <?php foreach ($jadwal_aktif as $j): ?>
            <div class="konseling-card">
              <?php if ($j['Status'] === 'Jadwalkan Ulang'): ?>
                <div class="urgent-badge">
                  <i class="bi bi-exclamation-triangle"></i> PERLU TINDAKAN
                </div>
              <?php endif; ?>
              
              <!-- HEADER DENGAN STATUS -->
              <div class="konseling-header status-<?= str_replace(' ', '-', $j['Status']) ?>">
                <?= strtoupper($j['Status']) ?>
                <?php if ($j['Status'] === 'Menunggu'): ?>
                  <i class="bi bi-clock"></i>
                <?php elseif ($j['Status'] === 'Disetujui'): ?>
                  <i class="bi bi-check-circle"></i>
                <?php elseif ($j['Status'] === 'Jadwalkan Ulang'): ?>
                  <i class="bi bi-arrow-clockwise"></i>
                <?php endif; ?>
              </div>
              
              <div class="konseling-body">
                <p class="mb-1 fw-semibold"><?= date("l, d F Y", strtotime($j['Tanggal_Konseling'])) ?></p>
                <p class="mb-1"><?= date("H:i", strtotime($j['Waktu_Konseling'])) ?> WIB</p>
                <p class="mb-1"><strong>Guru BK:</strong> <?= $j['nama_guru'] ?? '-' ?></p>
                <p class="mb-0"><strong>Topik:</strong> <?= $j['Topik_Konseling'] ?></p>
                
                <?php if (!empty($j['keterangan'])): ?>
                  <p class="mb-0 mt-2"><small><strong>Keterangan:</strong> <?= $j['keterangan'] ?></small></p>
                <?php endif; ?>

                <!-- TOMBOL JADWALKAN ULANG HANYA UNTUK STATUS JADWALKAN ULANG -->
                <?php if ($j['Status'] === 'Jadwalkan Ulang'): ?>
                  <button class="btn btn-reschedule" 
            onclick="openRescheduleModal(
                <?= $j['ID_Jadwal'] ?>, 
                '<?= addslashes($j['Tanggal_Konseling']) ?>', 
                '<?= addslashes($j['Waktu_Konseling']) ?>'
            )">
        Atur Ulang Jadwal
    </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        
        <button class="carousel-control next" onclick="slideCarousel('aktif', 1)">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    <?php else: ?>
      <p class="text-center">Belum ada jadwal aktif.</p>
    <?php endif; ?>

    <div class="text-start mt-3">
      <button class="btn btn-konseling" id="btnAjukanJadwal">
        Ajukan Jadwal Konseling Baru
      </button>
    </div>
  </div>

  <!-- RIWAYAT JADWAL -->
  <div class="schedule-wrapper">
    <div class="section-title">Riwayat Konseling</div>

    <?php if (count($riwayat_jadwal) > 0): ?>
      <div class="carousel-container">
        <button class="carousel-control prev" onclick="slideCarousel('riwayat', -1)">
          <i class="bi bi-chevron-left"></i>
        </button>
        
        <div class="carousel-wrapper" id="carouselRiwayat">
          <?php foreach ($riwayat_jadwal as $r): ?>
            <div class="konseling-card">
              <!-- HEADER DENGAN STATUS -->
              <div class="konseling-header status-<?= str_replace(' ', '-', $r['Status']) ?>">
                <?= strtoupper($r['Status']) ?>
                <?php if ($r['Status'] === 'Ditolak'): ?>
                  <i class="bi bi-x-circle"></i>
                <?php elseif ($r['Status'] === 'Selesai'): ?>
                  <i class="bi bi-check2-all"></i>
                <?php endif; ?>
              </div>
              
              <div class="konseling-body">
                <p class="mb-1 fw-semibold"><?= date("l, d F Y", strtotime($r['Tanggal_Konseling'])) ?></p>
                <p class="mb-1"><?= date("H:i", strtotime($r['Waktu_Konseling'])) ?> WIB</p>
                <p class="mb-1"><strong>Guru BK:</strong> <?= $r['nama_guru'] ?? '-' ?></p>
                <p class="mb-0"><strong>Topik:</strong> <?= $r['Topik_Konseling'] ?></p>
                
                <?php if (!empty($r['keterangan'])): ?>
                  <p class="mb-0 mt-2"><small><strong>Keterangan:</strong> <?= $r['keterangan'] ?></small></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        
        <button class="carousel-control next" onclick="slideCarousel('riwayat', 1)">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    <?php else: ?>
      <p class="text-center">Belum ada riwayat konseling.</p>
    <?php endif; ?>
     <!-- Tombol Kembali -->
        <a href="verifikasi_jadwal.php?logout=1" class="btn btn-outline-danger mt-4">
            <i class="bi bi-box-arrow-right"></i> Kembali
        </a>
  </div>

</div>

<!-- MODAL PENGAJUAN BARU -->
<div class="modal fade" id="modalAjukan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Ajukan Jadwal Konseling Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formPengajuan">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Siswa</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($siswa_data['nama']) ?>" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Kelas</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($siswa_data['kelas'] ?? '-') ?>" readonly>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label">Tanggal Konseling <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="tanggal" min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Topik Konseling <span class="text-danger">*</span></label>
            <select class="form-select" name="topik" required>
              <option value="" disabled selected>Pilih topik</option>
              <option value="Masalah Akademik">Masalah Akademik</option>
              <option value="Masalah Pergaulan">Masalah Pergaulan</option>
              <option value="Masalah Keluarga">Masalah Keluarga</option>
              <option value="Perencanaan Karir">Perencanaan Karir</option>
              <option value="Kesehatan Mental">Kesehatan Mental</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Waktu Konseling <span class="text-danger">*</span></label>
            <select class="form-select" name="jam" required>
              <option value="" disabled selected>Pilih waktu</option>
              <option value="07:00">07:00</option>
              <option value="08:00">08:00</option>
              <option value="09:00">09:00</option>
              <option value="10:00">10:00</option>
              <option value="11:00">11:00</option>
              <option value="13:00">13:00</option>
              <option value="14:00">14:00</option>
              <option value="15:00">15:00</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Guru BK <span class="text-danger">*</span></label>
            <select class="form-select" name="id_guru" required>
              <option value="" disabled selected>Pilih guru</option>
              <?php
              $stmt_guru = $pdo->prepare("SELECT id_guru, nama FROM guru WHERE status = 'aktif' ORDER BY nama ASC");
              $stmt_guru->execute();
              $guru_aktif = $stmt_guru->fetchAll();
              foreach ($guru_aktif as $guru) {
                  echo "<option value='{$guru['id_guru']}'>{$guru['nama']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan Tambahan</label>
            <textarea class="form-control" name="keterangan" rows="3" placeholder="Jelaskan secara singkat permasalahan yang ingin dikonsultasikan..."></textarea>
          </div>
          <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSubmit">
            <i class="bi bi-send"></i> Ajukan Jadwal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL JADWALKAN ULANG -->
<div class="modal fade" id="modalReschedule" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="bi bi-calendar2-event"></i> Jadwalkan Ulang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formReschedule">
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Silakan pilih tanggal dan waktu baru untuk konseling Anda.
          </div>
          
          <div class="mb-3">
            <label class="form-label">Tanggal Konseling Baru <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="tanggal" id="rescheduleTanggal" min="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Waktu Konseling Baru <span class="text-danger">*</span></label>
            <select class="form-select" name="jam" id="rescheduleJam" required>
              <option value="" disabled selected>Pilih waktu</option>
              <option value="07:00">07:00</option>
              <option value="08:00">08:00</option>
              <option value="09:00">09:00</option>
              <option value="10:00">10:00</option>
              <option value="11:00">11:00</option>
              <option value="13:00">13:00</option>
              <option value="14:00">14:00</option>
              <option value="15:00">15:00</option>
            </select>
          </div>

          <input type="hidden" name="id_jadwal" id="rescheduleIdJadwal">
          <input type="hidden" name="action" value="reschedule">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning" id="btnReschedule">
            <i class="bi bi-calendar-check"></i> Simpan Jadwal Baru
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
include 'footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function untuk membuka modal reschedule
function openRescheduleModal(id, tanggal, waktu) {
    console.log('Membuka modal reschedule:', id, tanggal, waktu);
    
    // Isi form modal dengan data
    document.getElementById('rescheduleIdJadwal').value = id;
    document.getElementById('rescheduleTanggal').value = tanggal;
    document.getElementById('rescheduleJam').value = waktu;
    
    // Tampilkan modal menggunakan Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('modalReschedule'));
    modal.show();
}

// Carousel functionality
const carouselStates = {
    aktif: { currentIndex: 0 },
    riwayat: { currentIndex: 0 }
};

function slideCarousel(type, direction) {
    const carousel = document.getElementById(`carousel${type.charAt(0).toUpperCase() + type.slice(1)}`);
    const cards = carousel.querySelectorAll('.konseling-card');
    const cardWidth = cards[0].offsetWidth + 20; // width + gap
    const visibleCards = 3;
    
    // Add sliding class for animation
    carousel.classList.add('sliding');
    
    carouselStates[type].currentIndex += direction;
    
    // Validate bounds
    const maxIndex = Math.max(0, cards.length - visibleCards);
    carouselStates[type].currentIndex = Math.max(0, Math.min(carouselStates[type].currentIndex, maxIndex));
    
    // Apply transform with smooth animation
    const translateX = -carouselStates[type].currentIndex * cardWidth;
    carousel.style.transform = `translateX(${translateX}px)`;
    
    // Remove sliding class after animation completes
    setTimeout(() => {
        carousel.classList.remove('sliding');
    }, 500);
    
    // Update button visibility
    updateCarouselButtons(type, cards.length, visibleCards);
}

function updateCarouselButtons(type, totalCards, visibleCards) {
    const prevBtn = document.querySelector(`#carousel${type.charAt(0).toUpperCase() + type.slice(1)}`).parentNode.querySelector('.prev');
    const nextBtn = document.querySelector(`#carousel${type.charAt(0).toUpperCase() + type.slice(1)}`).parentNode.querySelector('.next');
    
    if (prevBtn && nextBtn) {
        prevBtn.classList.toggle('hidden', carouselStates[type].currentIndex === 0);
        nextBtn.classList.toggle('hidden', carouselStates[type].currentIndex >= totalCards - visibleCards);
    }
}

// Event listener untuk modal pengajuan jadwal baru
document.addEventListener('DOMContentLoaded', function() {
    const btnAjukanJadwal = document.getElementById('btnAjukanJadwal');
    if (btnAjukanJadwal) {
        btnAjukanJadwal.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalAjukan'));
            modal.show();
        });
    }
    
    // Auto-hide toast notifications
    const toastElList = document.querySelectorAll('.toast');
    const toastList = [...toastElList].map(toastEl => new bootstrap.Toast(toastEl));
    toastList.forEach(toast => toast.show());
    
    // Initialize carousel buttons
    setTimeout(() => {
        updateCarouselButtons('aktif', document.querySelectorAll('#carouselAktif .konseling-card').length, 3);
        updateCarouselButtons('riwayat', document.querySelectorAll('#carouselRiwayat .konseling-card').length, 3);
    }, 100);
});
</script>

</body>
</html>