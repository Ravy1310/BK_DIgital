<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . "/../../includes/siswa_control/verification_handler.php";
validateAndRedirect('tes');

// Dapatkan data siswa dari session verifikasi
$siswa_data = getCurrentStudent();
$id_siswa = $siswa_data['id_siswa'];
$nama_siswa = $siswa_data['nama'];
$kelas_siswa = $siswa_data['kelas'];

// Include controller
require_once __DIR__ . "/../../includes/siswa_control/tes_controller.php";

// Dapatkan data dari controller
$daftarTes = getDaftarTesBelumDikerjakan($id_siswa); // Hanya tes yang belum dikerjakan
$riwayat = getRiwayatTes($id_siswa);
$error_tes = isset($_SESSION['error_tes']) ? $_SESSION['error_tes'] : '';
$error_riwayat = isset($_SESSION['error_riwayat']) ? $_SESSION['error_riwayat'] : '';

// Hapus session error
unset($_SESSION['error_tes'], $_SESSION['error_riwayat']);

// Bagi per slide
$tesPerSlide = array_chunk($daftarTes, 2);
$riwayatSlide = array_chunk($riwayat, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BK Digital - Tes BK</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Main Container */
    .main-container {
      background: white;
      border-radius: 24px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
      margin: 20px auto;
      padding: 0;
      overflow: hidden;
    }

    .content-wrapper {
      padding: 30px;
    }

    /* Header */
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

    /* Section Titles */
    .section-title {
      color: #004AAD;
      font-weight: 700;
      margin: 50px 0 30px;
      padding-bottom: 15px;
      border-bottom: 3px solid #f0f5ff;
      position: relative;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: -3px;
      left: 0;
      width: 80px;
      height: 3px;
      background: #004AAD;
      border-radius: 3px;
    }

    /* Judul Halaman */
    .judul-section {
      text-align: center;
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 30px;
      color: #004AAD;
    }

    /* Carousel Item */
    .carousel-item {
      padding-bottom: 20px;
    }

    /* Cards */
    .test-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      height: 100%;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 10px;
    }

    .test-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 74, 173, 0.15);
      border-color: #004AAD;
    }

    .test-card:before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #004AAD, #0066cc);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .test-card:hover:before {
      opacity: 1;
    }

    .riwayat-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      height: 100%;
      border-left: 5px solid #28a745;
      transition: all 0.3s ease;
      margin-bottom: 10px;
    }

    .riwayat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(40, 167, 69, 0.15);
    }

    .test-card h5, .riwayat-card h5 {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 12px;
    }

    /* Badge */
    .soal-badge {
      background: #e8f1ff;
      color: #004AAD;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      border: 2px solid #d0e1ff;
    }

    /* Buttons */
    .btn-tes {
      background: linear-gradient(135deg, #004AAD 0%, #0066cc 100%);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 25px;
      font-weight: 600;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
      text-align: center;
      font-size: 1rem;
    }

    .btn-tes:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 74, 173, 0.3);
      color: white;
    }

    .btn-hasil {
      background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 25px;
      font-weight: 600;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
      text-align: center;
      font-size: 1rem;
    }

    .btn-hasil:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
      color: white;
    }

    /* Carousel Controls */
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

    /* Nilai Box */
    .nilai-box {
      background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
      color: white;
      width: 55px;
      height: 55px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.2rem;
      box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: #6c757d;
      background: #f8f9fa;
      border-radius: 15px;
      border: 2px dashed #dee2e6;
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

    /* Tombol Kembali */
    .btn-kembali {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-kembali:hover {
      background: #c82333;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
      color: white;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .content-wrapper {
        padding: 20px;
      }
      
      .carousel-control-prev,
      .carousel-control-next {
        display: none;
      }
      
      .user-info {
        padding: 20px;
      }
      
      .judul-section {
        font-size: 28px;
      }
    }
    
    @media (max-width: 768px) {
      .content-wrapper {
        padding: 15px;
      }
      
      .container-main {
        padding: 10px;
      }
      
      .main-container {
        border-radius: 15px;
      }
      
      .section-title {
        margin: 40px 0 25px;
        font-size: 18px;
      }
      
      .judul-section {
        font-size: 24px;
        margin-bottom: 20px;
      }
      
      .user-info h5 {
        font-size: 1.2rem;
      }
    }

    /* ===================================================
   RESPONSIVE PERBAIKAN TAMBAHAN (FINAL)
   =================================================== */

/* ---------- HP KECIL (max 575px) ---------- */
@media (max-width: 575.98px) {

  /* Layout Wrapper */
  .content-wrapper {
    padding: 12px !important;
  }

  .container-main {
    padding: 5px !important;
  }

  .main-container {
    border-radius: 14px !important;
  }

  /* Judul halaman */
  .judul-section {
    font-size: 22px !important;
    margin-bottom: 18px !important;
  }

  /* Info Siswa */
  .user-info {
    padding: 15px !important;
    border-radius: 12px !important;
  }

  .user-info h5 {
    font-size: 1.1rem !important;
  }

  .user-info span {
    font-size: 0.85rem !important;
  }

  /* Kartu tes & riwayat jadi 1 kolom penuh */
  .carousel-item .col-lg-5,
  .carousel-item .col-md-6 {
    width: 100% !important;
    flex: 0 0 100% !important;
    max-width: 100% !important;
  }

  .test-card,
  .riwayat-card {
    padding: 18px !important;
  }

  /* Badge soal */
  .soal-badge {
    padding: 5px 10px !important;
    font-size: 0.8rem !important;
  }

  /* Tombol */
  .btn-tes,
  .btn-hasil {
    padding: 10px 18px !important;
    font-size: 0.9rem !important;
    border-radius: 8px !important;
  }

  /* Nilai box */
  .nilai-box {
    width: 45px !important;
    height: 45px !important;
    font-size: 1rem !important;
  }

  /* Hilangkan panah carousel */
  .carousel-control-prev,
  .carousel-control-next {
    display: none !important;
  }

  .section-title {
    font-size: 18px !important;
    margin: 32px 0 20px !important;
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

/* ---------- TABLET PORTRAIT (576px – 768px) ---------- */
@media (min-width: 576px) and (max-width: 768px) {

  .content-wrapper {
    padding: 18px !important;
  }

  .test-card,
  .riwayat-card {
    padding: 20px !important;
  }

  /* 1 atau 2 kartu (lebih lega) */
  .carousel-item .col-lg-5,
  .carousel-item .col-md-6 {
    flex: 0 0 80% !important;
    max-width: 80% !important;
    margin: 0 auto !important;
  }

  /* Panah tetap disembunyikan */
  .carousel-control-prev,
  .carousel-control-next {
    display: none !important;
  }
}

/* ---------- TABLET LANDSCAPE (769px – 992px) ---------- */
@media (min-width: 769px) and (max-width: 992px) {

  .carousel-item .col-lg-5,
  .carousel-item .col-md-6 {
    flex: 0 0 45% !important;
    max-width: 45% !important;
  }

  .carousel-control-prev,
  .carousel-control-next {
    display: none !important;
  }
}

  </style>
</head>

<body>

<div class="container-main">
  <div class="main-container">
    <div class="content-wrapper">
      <!-- Tombol Kembali -->
      
        <a href="verifikasi_tes.php?logout=1" class="btn btn-outline-danger mb-4">
          <i class="bi bi-box-arrow-right me-2"></i> Kembali
        </a>
      <h2 class="judul-section">Tes BK</h2>
      
      <!-- User Info -->
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

      <!-- ==========================
           CAROUSEL TES UTAMA (HANYA YANG BELUM DIKERJAKAN)
      ============================= -->
      <h4 class="section-title">
        <i class="fas fa-clipboard-list me-2"></i>Tes BK Tersedia
        <?php if (count($daftarTes) > 0): ?>
          <span class="badge bg-primary ms-2"><?= count($daftarTes) ?> Tes</span>
        <?php endif; ?>
      </h4>

      <?php if (!empty($error_tes)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_tes) ?></div>
      <?php endif; ?>

      <?php if (empty($daftarTes)): ?>
        <div class="empty-state">
          <i class="fas fa-clipboard-check fa-3x mb-4" style="color: #adb5bd;"></i>
          <h5 class="mb-3" style="color: #6c757d;">Tidak ada tes yang tersedia</h5>
          <p class="text-muted mb-0">Semua tes sudah Anda kerjakan. Lihat riwayat di bawah.</p>
        </div>
      <?php else: ?>
        <div id="carouselTesBK" class="carousel slide" data-bs-interval="false">
          <div class="carousel-inner">

            <?php foreach ($tesPerSlide as $index => $slide): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <div class="row justify-content-center g-4">

                <?php foreach ($slide as $tes): ?>
                  <div class="col-lg-5 col-md-6 d-flex">
                    <div class="test-card w-100">
                      <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0"><?= htmlspecialchars($tes['kategori_tes']) ?></h5>
                        <span class="soal-badge">
                          <i class="fas fa-question-circle me-1"></i><?= $tes['total_soal'] ?> Soal
                        </span>
                      </div>
                      
                      <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                        <?= htmlspecialchars($tes['deskripsi_tes']) ?>
                      </p>
                      
                      <div class="mt-4">
                        <a href="form_tes.php?id=<?= $tes['id_tes'] ?>" class="btn-tes w-100">
                          <i class="fas fa-play-circle me-2"></i>Mulai Tes
                        </a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
            <?php endforeach; ?>

          </div>

          <?php if (count($tesPerSlide) > 1): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselTesBK" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselTesBK" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- ==========================
           CAROUSEL RIWAYAT TES
      ============================= -->
      <h4 class="section-title">
        <i class="fas fa-history me-2"></i>Riwayat Tes
        <?php if (count($riwayat) > 0): ?>
          <span class="badge bg-success ms-2"><?= count($riwayat) ?> Hasil</span>
        <?php endif; ?>
      </h4>

      <?php if (!empty($error_riwayat)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_riwayat) ?></div>
      <?php endif; ?>

      <?php if (empty($riwayat)): ?>
        <div class="empty-state">
          <i class="fas fa-inbox fa-3x mb-4" style="color: #adb5bd;"></i>
          <h5 class="mb-3" style="color: #6c757d;">Belum ada riwayat tes</h5>
          <p class="text-muted mb-0">Mulai kerjakan tes untuk melihat hasilnya di sini</p>
        </div>
      <?php else: ?>

      <div id="carouselRiwayat" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">

          <?php foreach ($riwayatSlide as $index => $slide): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="row justify-content-center g-4">

              <?php foreach ($slide as $r): ?>
              <div class="col-lg-5 col-md-6 d-flex">
                <div class="riwayat-card w-100">
                  <div class="d-flex align-items-start mb-4">
                    <div class="nilai-box me-3">
                      <?= htmlspecialchars($r['nilai'] ?? '0') ?>
                    </div>
                    <div>
                      <h5 class="mb-2"><?= htmlspecialchars($r['kategori_tes']) ?></h5>
                      <p class="text-muted mb-0">
                        <i class="far fa-calendar-alt me-1"></i>
                        <?= date("d M Y - H:i", strtotime($r['tanggal_submit'])) ?>
                      </p>
                    </div>
                  </div>

                  <div class="mt-4">
                    <a href="hasil_tes.php?id=<?= htmlspecialchars($r['id_hasil']) ?>" class="btn-hasil w-100">
                      <i class="fas fa-chart-line me-2"></i>Lihat Hasil Lengkap
                    </a>
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
      
      <!-- Informasi jumlah riwayat -->
      <div class="mt-3 text-center">
        <p class="text-muted small">
          <i class="fas fa-info-circle me-1"></i>
          Menampilkan <?= count($riwayat) ?> hasil tes
        </p>
      </div>

      <?php endif; ?>
      
      
    </div>

    <div class="main-footer">
      <p class="mb-1">© 2025 BK Digital — SMA Al Islam Krian</p>
      <p class="small text-muted mb-0">Sistem Bimbingan dan Konseling Digital</p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../includes/js/siswa/tesbk.js"></script>

</body>
</html>