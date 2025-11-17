<?php
session_start();
require_once __DIR__ . "/../../includes/db_connection.php";

// CEK LOGIN SISWA
if (!isset($_SESSION['siswa_logged_in'])) {
    header("Location: verifikasi_jadwal.php");
    exit;
}

$id_siswa = $_SESSION['siswa_id'];

// ======================
// AMBIL JADWAL AKTIF
// ======================
$stmt = $pdo->prepare("
    SELECT jk.*, g.nama AS nama_guru
    FROM jadwal_konseling jk
    LEFT JOIN guru g ON jk.id_guru = g.id_guru
    WHERE jk.id_siswa = ? AND jk.status != 'selesai'
    ORDER BY jk.tanggal ASC
");
$stmt->execute([$id_siswa]);
$jadwal_aktif = $stmt->fetchAll();

// ======================
// AMBIL RIWAYAT
// ======================
$stmt2 = $pdo->prepare("
    SELECT jk.*, g.nama AS nama_guru
    FROM jadwal_konseling jk
    LEFT JOIN guru g ON jk.id_guru = g.id_guru
    WHERE jk.id_siswa = ? AND jk.status = 'selesai'
    ORDER BY jk.tanggal DESC
");
$stmt2->execute([$id_siswa]);
$riwayat = $stmt2->fetchAll();

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Konseling</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ====================== STYLE DESAIN AWAL ====================== -->
  <style>
    body {
     background: url('../../assets/image/background.jpg');
      font-family: 'Poppins', sans-serif;
    }

    .schedule-wrapper {
      background: #ffffffd0;
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

    .konseling-card {
      width: 32%;
      background: #fff;
      display: inline-block;
      margin-right: 1%;
      margin-bottom: 20px;
      border-radius: 15px;
      padding: 0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      vertical-align: top;
    }

    .konseling-header {
      background: #0d47a1;
      padding: 12px 18px;
      color: #fff;
      font-weight: 600;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .konseling-body {
      padding: 18px 18px 22px 18px;
    }

    .btn-konseling {
      background: #0d47a1;
      color: #fff;
      padding: 12px 25px;
      font-weight: 600;
      border-radius: 10px;
    }

    /* Animasi Fade In */
@keyframes fadeIn {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.konseling-card {
  animation: fadeIn 0.6s ease forwards;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

/* Hover: Kartu terangkat */
.konseling-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 22px rgba(0,0,0,0.18);
}

/* Animasi Header Slide dari kiri */
@keyframes slideIn {
  0% { opacity: 0; transform: translateX(-15px); }
  100% { opacity: 1; transform: translateX(0); }
}

.konseling-header {
  animation: slideIn 0.5s ease-out;
}

/* Button animasi */
.btn-konseling {
  transition: background 0.25s ease, transform 0.25s ease;
}

.btn-konseling:hover {
  background: #08306b;
  transform: translateY(-3px);
}

  </style>
</head>

<body>

<div class="container py-4">

  <!-- =================== JADWAL AKTIF =================== -->
  <div class="schedule-wrapper">
    <div class="section-title">Jadwal Konseling</div>

    <?php if (count($jadwal_aktif) > 0): ?>
      <?php foreach ($jadwal_aktif as $j): ?>
        <div class="konseling-card">
          <div class="konseling-header">
            <?= ucfirst($j['status']) ?>
            <i class="bi bi-clock-history"></i>
          </div>

          <div class="konseling-body">
            <p class="mb-1 fw-semibold">
              <?= date("l, d F Y", strtotime($j['tanggal'])) ?>
            </p>

            <p class="mb-1"><?= $j['jam'] ?> WIB</p>

            <p class="mb-0">
              <strong>Guru BK:</strong> <?= $j['nama_guru'] ?? '-' ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Belum ada jadwal aktif.</p>
    <?php endif; ?>

    <div class="text-start mt-3">
      <a href="pengajuantemu.php" class="btn btn-konseling">
        Ajukan Jadwal Konseling Baru
      </a>
    </div>
  </div>


  <!-- =================== RIWAYAT =================== -->
  <div class="schedule-wrapper">
    <div class="section-title">Riwayat Konseling</div>

    <?php if (count($riwayat) > 0): ?>
      <?php foreach ($riwayat as $r): ?>
        <div class="konseling-card">
          <div class="konseling-header">
            Konseling Selesai
            <i class="bi bi-clock-history"></i>
          </div>

          <div class="konseling-body">
            <p class="mb-1 fw-semibold">
              <?= date("l, d F Y", strtotime($r['tanggal'])) ?>
            </p>

            <p><?= $r['jam'] ?> WIB</p>

            <p><strong>Guru BK:</strong> <?= $r['nama_guru'] ?? '-' ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Belum ada riwayat.</p>
    <?php endif; ?>

    <?php if (count($riwayat) > 0): ?>
  <div class="text-start mt-3">
    <a href="riwayat_konseling.php" class="btn btn-konseling">
      Lihat Semua Riwayat Konseling
    </a>
  </div>
<?php endif; ?>
</div>

</body>
</html>
