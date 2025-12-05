<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once "../../includes/db_connection.php";

// Validasi session verifikasi
require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";
validateAndRedirect('pengaduan');

// Dapatkan data siswa dari session verifikasi
$siswa_data = getCurrentStudent();
$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];
$kelas_siswa = $siswa_data['kelas'];

// VARIABEL UNTUK PESAN POPUP (dari session)
$popup_type = $_SESSION['popup_type'] ?? '';
$popup_message = $_SESSION['popup_message'] ?? '';

// Hapus session popup setelah diambil
unset($_SESSION['popup_type'], $_SESSION['popup_message']);

// PROSES FORM PENGADUAN JIKA ADA POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_pengaduan'])) {
    try {
        // Ambil data dari form
        $id_siswa_form = $_POST['id_siswa'] ?? null;
        $anonim_form = isset($_POST['anonim']) ? (int)$_POST['anonim'] : 0;
        $jenis_laporan = $_POST['jenis_laporan'] ?? '';
        $jenis_kejadian = $_POST['jenis_kejadian'] ?? '';
        $penjelasan = trim($_POST['penjelasan'] ?? '');
        
        // Validasi data
        if (empty($jenis_laporan) || empty($jenis_kejadian) || empty($penjelasan)) {
            throw new Exception("Semua field harus diisi!");
        }
        
        // Validasi panjang penjelasan
        if (strlen($penjelasan) < 20) {
            throw new Exception("Penjelasan harus minimal 20 karakter!");
        }
        
        // LOGIKA UNTUK TERIDENTIFIKASI
        if ($anonim_form == 0) { // Mode TERIDENTIFIKASI
            // Validasi: Harus ada ID siswa
            if (empty($id_siswa_form)) {
                throw new Exception("ID siswa tidak valid untuk pengaduan teridentifikasi!");
            }
            
            // Validasi: Pastikan siswa ada di database
            $stmt_check = $pdo->prepare("SELECT nama, kelas FROM siswa WHERE id_siswa = ?");
            $stmt_check->execute([$id_siswa_form]);
            $siswa_data_form = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            if (!$siswa_data_form) {
                throw new Exception("Siswa dengan ID tersebut tidak ditemukan!");
            }
            
            // Ambil nama dan kelas dari database
            $nama_siswa_form = $siswa_data_form['nama'];
            $kelas_siswa_form = $siswa_data_form['kelas'];
            
            // Insert ke database DENGAN id_siswa yang valid
            $sql = "INSERT INTO pengaduan 
                    (id_siswa, jenis_laporan, jenis_kejadian, deskripsi, nama_siswa, kelas_siswa, status, tanggal_pengaduan) 
                    VALUES 
                    (:id_siswa, :jenis_laporan, :jenis_kejadian, :deskripsi, :nama_siswa, :kelas_siswa, 'menunggu', NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':id_siswa' => $id_siswa_form,
                ':jenis_laporan' => $jenis_laporan,
                ':jenis_kejadian' => $jenis_kejadian,
                ':deskripsi' => $penjelasan,
                ':nama_siswa' => $nama_siswa_form,
                ':kelas_siswa' => $kelas_siswa_form
            ]);
            
            $_SESSION['popup_type'] = 'success';
            $_SESSION['popup_message'] = "Pengaduan teridentifikasi berhasil dikirim! Status: Menunggu";
            
        } else { // Mode ANONIM
            // Set semua data siswa menjadi NULL atau 'Anonim'
            $nama_siswa_form = 'Anonim';
            $kelas_siswa_form = 'Anonim';
            
            // Insert ke database DENGAN id_siswa = NULL
            $sql = "INSERT INTO pengaduan 
                    (id_siswa, jenis_laporan, jenis_kejadian, deskripsi, nama_siswa, kelas_siswa, status, tanggal_pengaduan) 
                    VALUES 
                    (NULL, :jenis_laporan, :jenis_kejadian, :deskripsi, :nama_siswa, :kelas_siswa, 'menunggu', NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':jenis_laporan' => $jenis_laporan,
                ':jenis_kejadian' => $jenis_kejadian,
                ':deskripsi' => $penjelasan,
                ':nama_siswa' => $nama_siswa_form,
                ':kelas_siswa' => $kelas_siswa_form
            ]);
            
            $_SESSION['popup_type'] = 'success';
            $_SESSION['popup_message'] = "Pengaduan anonim berhasil dikirim! Status: Menunggu";
        }
        
        // REDIRECT UNTUK MENCEGAH FORM RESUBMIT
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $_SESSION['popup_type'] = 'error';
        $_SESSION['popup_message'] = "Gagal mengirim pengaduan: " . $e->getMessage();
        // Tidak redirect jika error agar user bisa perbaiki data
    }
}

// ==========================
// AMBIL RIWAYAT PENGADUAN (HANYA UNTUK TERIDENTIFIKASI)
// ==========================
$riwayat = [];
if ($id_siswa) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM pengaduan 
            WHERE id_siswa = ? 
            ORDER BY tanggal_pengaduan DESC
        ");
        $stmt->execute([$id_siswa]);
        $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_db = "Error: " . $e->getMessage();
    }
}

// Bagi riwayat menjadi slide (2 item per slide)
$riwayatSlide = array_chunk($riwayat, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Pengaduan - BK Digital</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    background: url('../../assets/image/background.jpg') center/cover no-repeat;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}
.main-wrapper {
    background: white;
    padding: 45px;
    border-radius: 24px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    margin-top: 40px;
}
.judul-section {
    text-align:center;
    font-size: 32px;
    font-weight:700;
    
}
.pengaduan-card {
    background:white;
    border-radius:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    transition:0.25s;
    border:1px solid #d7e0ef;
    margin-bottom: 15px;
}
.pengaduan-card:hover {
    transform: translateY(-5px);
    box-shadow:0 8px 18px rgba(0,0,0,0.15);
}
.card-header-custom {
    background:#0050BC;
    padding:12px 18px;
    color:white;
    font-size:16px;
    font-weight:600;
}
.card-body-custom {
    padding:18px;
    font-size:15px;
}
.footer-info {
    padding:12px 18px;
    background:#f5f8ff;
    font-size:14px;
    color:#555;
    display:flex;
    align-items:center;
    gap:6px;
}
.btn-primary {
    background:#003893;
    border:none;
    font-weight:600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background:#002d73;
    transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
}
.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.status-menunggu {
    background-color: #ffc107;
    color: #000;
}
.status-diproses {
    background-color: #0dcaf0;
    color: #000;
}
.status-selesai {
    background-color: #198754;
    color: white;
}
.status-ditolak {
    background-color: #dc3545;
    color: white;
}

/* POPUP NOTIFIKASI */
.popup-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    max-width: 400px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 9999;
    transform: translateX(120%);
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 5px solid #003893;
}

.popup-notification.show {
    transform: translateX(0);
}

.popup-notification.success {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #f8f9fa 100%);
}

.popup-notification.error {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #f8f9fa 100%);
}

.popup-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.popup-notification.success .popup-icon {
    background: #28a745;
    color: white;
}

.popup-notification.error .popup-icon {
    background: #dc3545;
    color: white;
}

.popup-content {
    flex: 1;
}

.popup-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 3px;
}

.popup-message {
    font-size: 14px;
    color: #495057;
}

.popup-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.popup-close:hover {
    background: rgba(0,0,0,0.1);
    color: #343a40;
}

/* STYLE UNTUK FIELD ANONIM */
.field-anonim {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

.field-anonim p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
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
    
/* CAROUSEL STYLES */
.carousel-item {
    padding-bottom: 20px;
}

.carousel-control-prev,
.carousel-control-next {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.9;
}

.carousel-control-prev {
    left: -25px;
}

.carousel-control-next {
    right: -25px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(25%) sepia(100%) saturate(1000%) hue-rotate(200deg);
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Responsive untuk carousel */
@media (max-width: 992px) {
    .carousel-control-prev,
    .carousel-control-next {
        display: none;
    }
}

/* Empty state untuk riwayat */
.empty-riwayat {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 15px;
    border: 2px dashed #dee2e6;
}

.empty-riwayat i {
    font-size: 3rem;
    color: #adb5bd;
    margin-bottom: 20px;
}

.empty-riwayat h5 {
    color: #6c757d;
    margin-bottom: 10px;
}

/* ===========================
   RESPONSIVE FIXES
   =========================== */

/* Wrapper agar tidak terlalu melebar */
@media (max-width: 768px) {
    .main-wrapper {
        padding: 25px;
        margin-top: 20px;
    }

    .judul-section {
        font-size: 24px;
    }

    .user-info {
        padding: 18px;
    }

    .pengaduan-card {
        margin-bottom: 20px;
    }
}

/* Carousel supaya kartu full 1 kolom di mobile */
@media (max-width: 576px) {
    .carousel-item .col-lg-5,
    .carousel-item .col-md-6 {
        width: 100% !important;
        max-width: 100%;
        flex: 0 0 100%;
    }

    .carousel-control-prev,
    .carousel-control-next {
        display: none !important;
    }

    .card-header-custom {
        font-size: 14px;
        padding: 10px 14px;
    }

    .card-body-custom {
        font-size: 14px;
        padding: 14px;
    }

    .footer-info {
        font-size: 13px;
        padding: 10px 14px;
    }
}

/* Judul modal di HP */
@media (max-width: 480px) {
    .modal-dialog {
        margin: 15px;
    }

    .modal-body {
        padding: 15px;
    }

    .modal-title {
        font-size: 18px;
    }

    textarea.form-control {
        font-size: 14px;
    }
}

/* Tombol kembali biar tidak terlalu besar */
@media (max-width: 430px) {
    .btn-outline-danger {
        max-width: 180px;   /* batas panjang tombol */
        width: auto;        /* biar tidak full */
        display: inline-block;
        margin-bottom: 15px;
        text-align: left;   /* tombol tetap di kiri */
    }
}

</style>
</head>

<body>

<!-- POPUP NOTIFIKASI -->
<?php if (!empty($popup_type)): ?>
<div id="notificationPopup" class="popup-notification <?= $popup_type ?> show">
    <div class="popup-icon">
        <i class="<?= $popup_type === 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-xmark-circle' ?>"></i>
    </div>
    <div class="popup-content">
        <div class="popup-title">
            <?= $popup_type === 'success' ? 'Berhasil!' : 'Gagal!' ?>
        </div>
        <div class="popup-message"><?= htmlspecialchars($popup_message) ?></div>
    </div>
    <button class="popup-close" onclick="hideNotification()">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
<?php endif; ?>

<div class="container">
    <div class="main-wrapper">
 <!-- Tombol Kembali -->
        <a href="verifikasi_pengaduan.php?logout=1" class="btn btn-outline-danger mt-4">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
        <h2 class="judul-section mb-4">Riwayat Pengaduan</h2>

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
                    <!-- Kosong untuk alignment -->
                </div>
            </div>
        </div>
        
        <!-- Button untuk buka modal pengaduan -->
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#pengaduanModal">
            <i class="fas fa-plus-circle me-2"></i> Buat Pengaduan Baru
        </button>
       
        <h4 class="mb-3">Riwayat Pengaduan Anda</h4>
        <hr>

        <?php if (isset($error_db)): ?>
            <div class="alert alert-danger"><?= $error_db ?></div>
        <?php endif; ?>

        <?php if (empty($riwayat)): ?>
            <div class="empty-riwayat">
                <i class="fas fa-inbox fa-3x mb-4"></i>
                <h5 class="mb-2">Belum ada pengaduan</h5>
                <p class="text-muted mb-0">Anda belum pernah membuat pengaduan sebelumnya</p>
            </div>
        <?php else: ?>
            <div id="carouselRiwayat" class="carousel slide" data-bs-interval="false">
                <div class="carousel-inner">
                    
                    <?php foreach ($riwayatSlide as $index => $slide): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row justify-content-center g-4">
                            
                            <?php foreach ($slide as $r): ?>
                            <?php  
                            $status = strtolower($r["status"] ?? "menunggu");
                            $status_class = "status-" . $status;
                            $status_text = ucfirst($status);
                            ?>
                            <div class="col-lg-5 col-md-6 d-flex">
                                <div class="pengaduan-card w-100">
                                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('d/m/Y', strtotime($r["tanggal_pengaduan"])) ?>
                                        </span>
                                        <span class="status-badge <?= $status_class ?>">
                                            <?= $status_text ?>
                                        </span>
                                    </div>
                                    <div class="card-body-custom">
                                        <p class="mb-2"><strong>Jenis Laporan:</strong> <?= htmlspecialchars($r["jenis_laporan"]) ?></p>
                                        <p class="mb-2"><strong>Jenis Kejadian:</strong> <?= htmlspecialchars($r["jenis_kejadian"]) ?></p>
                                        <p class="mb-2"><strong>Deskripsi:</strong></p>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars(substr($r["deskripsi"], 0, 150))) ?><?= strlen($r["deskripsi"]) > 150 ? '...' : '' ?></p>
                                    </div>
                                    <div class="footer-info d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            <?= date('H:i', strtotime($r["tanggal_pengaduan"])) ?>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                </div>
                
                <?php if (count($riwayatSlide) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
                
            </div>
            
          
        <?php endif; ?>
        
       
    </div>
</div>

<!-- MODAL PENGADUAN BARU -->
<div class="modal fade" id="pengaduanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comment-dots me-2"></i> Buat Pengaduan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPengaduan" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id_siswa" id="idSiswaInput" value="<?= $id_siswa ?>">
                    <input type="hidden" id="anonimInput" name="anonim" value="0">
                    <input type="hidden" name="submit_pengaduan" value="1">

                    <div class="mb-3">
                        <label class="form-label">Jenis Laporan</label>
                        <select id="jenisAduan" name="jenis_laporan" class="form-select" required onchange="toggleIdentifikasiFields()">
                            <option value="Teridentifikasi" selected>Teridentifikasi</option>
                            <option value="Anonim">Anonim</option>
                        </select>
                    </div>

                    <!-- Field identifikasi siswa (default muncul) -->
                    <div id="identifikasiFields">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($nama_siswa) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($kelas_siswa) ?>" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Field untuk anonim (default tersembunyi) -->
                    <div id="anonimFields" class="field-anonim" style="display: none;">
                        <p><i class="fas fa-user-secret me-1"></i> <strong>Mode Anonim:</strong> Identitas Anda akan disembunyikan. Pengaduan tidak akan tercatat dalam riwayat pribadi.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kejadian</label>
                        <select name="jenis_kejadian" class="form-select" required>
                            <option value="" disabled selected>Pilih jenis kejadian</option>
                            <option value="Bully">Bully</option>
                            <option value="Kekerasan Fisik">Kekerasan Fisik</option>
                            <option value="Kekerasan Verbal">Kekerasan Verbal</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Penjelasan</label>
                        <textarea name="penjelasan" class="form-control" rows="5" required placeholder="Jelaskan kejadian secara detail..."></textarea>
                        <div class="form-text">Minimal 20 karakter</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
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
    // Fungsi untuk menyembunyikan notifikasi
    function hideNotification() {
        const popup = document.getElementById('notificationPopup');
        if (popup) {
            popup.classList.remove('show');
            setTimeout(() => {
                popup.style.display = 'none';
            }, 400);
        }
    }
    
    // Auto hide notifikasi setelah 5 detik
    <?php if (!empty($popup_type)): ?>
    setTimeout(() => {
        hideNotification();
    }, 5000);
    <?php endif; ?>
    
    // Fungsi untuk toggle field identifikasi/anonim
    function toggleIdentifikasiFields() {
        const jenisLaporan = document.getElementById('jenisAduan').value;
        const identifikasiFields = document.getElementById('identifikasiFields');
        const anonimFields = document.getElementById('anonimFields');
        const anonimInput = document.getElementById('anonimInput');
        
        if (jenisLaporan === 'Anonim') {
            identifikasiFields.style.display = 'none';
            anonimFields.style.display = 'block';
            anonimInput.value = '1';
        } else {
            identifikasiFields.style.display = 'block';
            anonimFields.style.display = 'none';
            anonimInput.value = '0';
        }
    }
    
    // Reset form modal ketika ditutup
    document.getElementById('pengaduanModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPengaduan').reset();
        // Reset ke mode teridentifikasi
        document.getElementById('jenisAduan').value = 'Teridentifikasi';
        toggleIdentifikasiFields();
    });
    
    // Validasi form sebelum submit
    document.getElementById('formPengaduan').addEventListener('submit', function(e) {
        const penjelasan = document.querySelector('textarea[name="penjelasan"]').value;
        const jenisKejadian = document.querySelector('select[name="jenis_kejadian"]').value;
        
        if (jenisKejadian === "" || jenisKejadian === null) {
            e.preventDefault();
            alert("Harap pilih jenis kejadian!");
            return false;
        }
        
        if (penjelasan.length < 20) {
            e.preventDefault();
            alert("Penjelasan harus minimal 20 karakter!");
            return false;
        }
        
        // Tampilkan loading
        const submitBtn = document.querySelector('#formPengaduan button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
        submitBtn.disabled = true;
        
        // Tutup modal setelah submit
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('pengaduanModal'));
            if (modal) {
                modal.hide();
            }
        }, 500);
        
        return true;
    });
    
    // Inisialisasi toggle saat modal dibuka
    document.getElementById('pengaduanModal').addEventListener('show.bs.modal', function () {
        toggleIdentifikasiFields();
    });
    
    // Inisialisasi carousel jika ada lebih dari 1 slide
    <?php if (count($riwayatSlide) > 1): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = new bootstrap.Carousel(document.getElementById('carouselRiwayat'), {
            interval: false
        });
    });
    <?php endif; ?>
</script>

</body>
</html>