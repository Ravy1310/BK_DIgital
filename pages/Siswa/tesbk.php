<?php
session_start();
require_once "../../includes/db_connection.php";

// pastikan sudah verifikasi ID
if (!isset($_SESSION['siswa_id'])) {
    header("Location: verifikasi.php");
    exit;
}

$id_siswa = $_SESSION['siswa_id'];

// =========================
// AMBIL SEMUA TES
// =========================
$stmt = $pdo->prepare("SELECT * FROM tes ORDER BY id_tes ASC");
$stmt->execute();
$daftarTes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Bagi per slide
$tesPerSlide = array_chunk($daftarTes, 2);

// =========================
// AMBIL RIWAYAT TES SISWA
// =========================
$q = $pdo->prepare("
    SELECT h.*, t.kategori_tes 
    FROM hasil_tes h
    JOIN tes t ON h.id_tes = t.id_tes
    WHERE h.id_siswa = ?
    ORDER BY h.tanggal_submit DESC
");
$q->execute([$id_siswa]);
$riwayat = $q->fetchAll(PDO::FETCH_ASSOC);

// Pisahkan 2 card per slide
$riwayatSlide = array_chunk($riwayat, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BK Digital - Tes BK</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: url('../../assets/image/background.jpg');
      font-family: 'Poppins', sans-serif;
      padding: 0;
      margin: 0;
      min-height: 100vh;
    }

    .section-title {
      text-align: center;
      font-weight: 700;
      margin: 40px 0 25px;
      color: #000;
    }

    .section-container {
      background: #dce7ff;
      padding: 30px;
      border-radius: 20px;
      width: 90%;
      max-width: 1100px;
      margin: auto;
      margin-bottom: 50px;
    }

    .test-card, .riwayat-card {
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
    }

    .btn-utama {
      background: #004AAD;
      color: #fff;
      font-weight: 600;
      border-radius: 8px;
      padding: 8px;
      text-align: center;
      display: block;
    }

    .carousel-item {
      padding: 10px 20px;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      filter: invert(1);
    }
  </style>
</head>

<body>

  <div class="container py-4">

    <!-- ==========================
         CAROUSEL TES UTAMA
    ============================= -->
    <h3 class="section-title">Tes BK</h3>

    <div class="section-container">
      <div id="carouselTesBK" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">

          <?php foreach ($tesPerSlide as $index => $slide): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="row justify-content-center g-4">

              <?php foreach ($slide as $tes): ?>
                <?php  
                $q = $pdo->prepare("SELECT COUNT(*) FROM soal_tes WHERE id_tes = ?");
                $q->execute([$tes['id_tes']]);
                $totalSoal = $q->fetchColumn();
                ?>
                <div class="col-md-5 d-flex">
                  <div class="test-card w-100">
                    <div>
                      <h5><?= htmlspecialchars($tes['kategori_tes']) ?></h5>
                      <p><?= htmlspecialchars($tes['deskripsi_tes']) ?></p>
                      <p class="text-muted small">Total Soal: <?= $totalSoal ?></p>
                    </div>
                    <a href="form_tes.php?id=<?= $tes['id_tes'] ?>" class="btn-utama">Mulai Tes</a>
                  </div>
                </div>
              <?php endforeach; ?>

            </div>
          </div>
          <?php endforeach; ?>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTesBK" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselTesBK" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>


    <!-- ==========================
         CAROUSEL RIWAYAT TES
    ============================= -->
    <h3 class="section-title">Riwayat Tes Kamu</h3>

    <div class="section-container">

      <?php if (count($riwayat) == 0): ?>
        <p class="text-center text-muted">Belum ada riwayat tes.</p>
      <?php else: ?>

      <div id="carouselRiwayat" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner">

          <?php foreach ($riwayatSlide as $index => $slide): ?>
          <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="row justify-content-center g-4">

              <?php foreach ($slide as $r): ?>
              <div class="col-md-5 d-flex">
                <div class="riwayat-card w-100">
                  <div>
                    <h5><?= htmlspecialchars($r['kategori_tes']) ?></h5>
                    <p class="text-muted mb-1">
                      Tanggal: <?= date("d M Y - H:i", strtotime($r['tanggal_submit'])) ?>
                    </p>

                    <!-- Aman: akan menampilkan '-' jika tidak ada kolom nilai -->
                    <p><strong>Nilai:</strong>
                      <?= htmlspecialchars($r['nilai'] ?? '-') ?>
                    </p>
                  </div>

                  <a href="hasil_tes.php?id=<?= htmlspecialchars($r['id_hasil']) ?>" class="btn btn-success w-100">
                    Lihat Hasil
                  </a>
                </div>
              </div>
              <?php endforeach; ?>

            </div>
          </div>
          <?php endforeach; ?>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselRiwayat" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>

      <?php endif; ?>

    </div>

  </div>

  <footer class="text-center text-muted pb-3">
    © 2025 BK Digital — SMA Al Islam Krian
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
