<?php 
include 'header.php';
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* ===================== BODY PADDING UNTUK NAVBAR FIXED ===================== */
  body {
      padding-top: 0px; /* sesuaikan tinggi navbar */
      background: url('../../assets/image/background.jpg');
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
      animation: fadeIn 0.6s ease forwards;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .konseling-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 22px rgba(0,0,0,0.18);
  }

  .konseling-header {
      background: #0050BC;
      padding: 12px 18px;
      color: #fff;
      font-weight: 600;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      animation: slideIn 0.5s ease-out;
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
      transition: background 0.25s ease, transform 0.25s ease;
  }

  .btn-konseling:hover {
      background: #0050BC;
      transform: translateY(-3px);
  }

  @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
  }

  @keyframes slideIn {
      0% { opacity: 0; transform: translateX(-15px); }
      100% { opacity: 1; transform: translateX(0); }
  }

/* ===================== MODAL CUSTOM HEIGHT ===================== */
#modalAjukan .modal-dialog {
    max-width: 450px;   /* Lebar modal lebih kecil */
    max-height: 80vh;   /* Tinggi maksimal 80% dari viewport */
}

#modalAjukan .modal-content {
    border-radius: 15px;
    height: 100%;       /* Gunakan tinggi penuh dialog */
}

#modalAjukan .modal-body {
    max-height: calc(80vh - 150px); /* Kurangi header/footer agar body scrollable */
    overflow-y: auto;    /* Scroll vertikal jika isinya terlalu panjang */
    padding: 15px;
}

#modalAjukan .modal-footer {
    padding: 10px 15px;
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
            <p class="mb-1 fw-semibold"><?= date("l, d F Y", strtotime($j['tanggal'])) ?></p>
            <p class="mb-1"><?= $j['jam'] ?> WIB</p>
            <p class="mb-0"><strong>Guru BK:</strong> <?= $j['nama_guru'] ?? '-' ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Belum ada jadwal aktif.</p>
    <?php endif; ?>

    <div class="text-start mt-3">
      <button class="btn btn-konseling" data-bs-toggle="modal" data-bs-target="#modalAjukan">
        Ajukan Jadwal Konseling Baru
      </button>
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
            <p class="mb-1 fw-semibold"><?= date("l, d F Y", strtotime($r['tanggal'])) ?></p>
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
        <a href="riwayat_konseling.php" class="btn btn-konseling">Lihat Semua Riwayat Konseling</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ===================== MODAL ===================== -->
<div class="modal fade" id="modalAjukan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Ajukan Jadwal Konseling</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="pengajuantemu.php">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama"
                   value="<?= $_SESSION['siswa_nama'] ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Tanggal Bimbingan</label>
            <input type="date" class="form-control" name="tanggal" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Topik Konseling</label>
            <select class="form-select" name="topik" required>
              <option disabled selected>Pilih topik</option>
              <option>Masalah Akademik</option>
              <option>Masalah Pergaulan</option>
              <option>Masalah Keluarga</option>
              <option>Perencanaan Karir</option>
              <option>Kesehatan Mental</option>
              <option>Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Jam</label>
            <select class="form-select" name="jam" required>
              <option disabled selected>Pilih jam</option>
              <option>07:00</option>
              <option>08:00</option>
              <option>09:00</option>
              <option>10:00</option>
              <option>11:00</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Guru BK</label>
            <select class="form-select" name="id_guru" required>
              <option disabled selected>Pilih guru</option>
              <option value="6">Budi Santoso</option>
              <option value="7">Siti Rahayu</option>
              <option value="16">Rafi Isnanto</option>
            </select>
          </div>

          <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Navbar shrink effect -->
<script>
  window.addEventListener("scroll", function () {
    const nav = document.querySelector(".navbar");
    if (window.scrollY > 10) nav.classList.add("scrolled");
    else nav.classList.remove("scrolled");
  });
</script>

</body>
</html>
